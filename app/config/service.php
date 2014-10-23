<?php

/*
 +----------------------------------------------------------------------------------+
 | PhalconJumpstart                                                                 |
 +----------------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 PhalconJumpstart Team (http://phalconjumpstart.com)      |
 +----------------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled               |
 | with this package in the file docs/LICENSE.txt.                                  |
 |                                                                                  |
 | If you did not receive a copy of the license and are unable to                   |
 | obtain it through the world-wide-web, please send an email                       |
 | to license@phalconjumpstart.com so we can send you a copy immediately.           |
 +----------------------------------------------------------------------------------+
*/

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new \Phalcon\DI\FactoryDefault();

/**
 * Register the configuration itself as a service
 */
$di->setShared('config', function() use ($config) {
    return $config;
});

/**
 * Register the settings itself as a service
 */
$di->setShared('setting', function() use ($setting) {
    return $setting;
});

/**
 * Register the settings itself as a service
 */
$di->setShared('perm', function() use ($perm) {
    return $perm;
});

/**
 * Router
 */
$di->set('router', function() {
    return require_once ROOT_PATH . '/app/config/routes.php';
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function() use ($config) {
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Profiling execute query time
 */
if (isset($_GET['uprofiler'])) {
    $di->set('profiler', function(){
        return new \Phalcon\Db\Profiler();
    }, true);
}

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function() use ($config, $di) {

    if (isset($_GET['uprofiler'])) {
        $eventsManager = new \Phalcon\Events\Manager();
        
        //Get a shared instance of the DbProfiler
        $profiler = $di->getProfiler();

        //Listen all the database events
        $eventsManager->attach('db', function($event, $connection) use ($profiler) {
            if ($event->getType() == 'beforeQuery') {
                $profiler->startProfile($connection->getSQLStatement());
            }
            if ($event->getType() == 'afterQuery') {
                $profiler->stopProfile();
            }
        });
    }

    $db = new \Phalcon\Db\Adapter\Pdo\Mysql([
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        )

    ]);

    if (isset($_GET['uprofiler'])) {
        //Assign the eventsManager to the db adapter instance
        $db->setEventsManager($eventsManager);
    }
    
    return $db;
});

/**
 * Models Metadata adapter
 */
$di->set('modelsMetadata', function() use ($config) {
    $metadata = new \Phalcon\Mvc\Model\Metadata\Apc([
        'lifetime' => 3600,
        'prefix' => $config->database->dbname . '.'
    ]);

    return $metadata;
});

/**
 * Setting up view component with Twig
 */
$di->setShared('view', function() use ($config) {
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir($config->application->viewsDir);
    $view->registerEngines([
        '.phtml' => '\Phalcon\Mvc\View\Engine\Php',
        '.volt' => function($view, $di) use ($config) {
            $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir . 'view/',
                'compiledExtension' => '.compiled',
                'compileAlways' => true
            ));

            $compiler = $volt->getCompiler();
            $compiler->addFilter('floor', 'floor');
            $compiler->addFunction('range', 'range');

            return $volt;
        },
    ]);

    return $view;
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function() {
    $session = new \Phalcon\Session\Adapter\Files();
    $session->start();

    return $session;
});

/**
 * Custom dispatcher (override the default)
 */
$di->set('dispatcher', function() use ($di, $perm) {
    $eventsManager = $di->getShared('eventsManager');

    // Custom ACL class
    $permission = new \Jumpstart\Permission($perm);

    // Listen for event from Permission class
    $eventsManager->attach('dispatch', $permission);

    //Attach a listener
    $eventsManager->attach('dispatch:beforeException', function($event, $dispatcher, $exception) {

        //Alternative way, controller or action doesn't exist
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(array(
                        'namespace' =>  'Controller\Site',
                        'controller' => 'index',
                        'action' => 'notfound'
                    ));
                    return false;
            }
        }
    });

    $dispatcher = new \Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});

/**
 * Register the flash service with custom CSS classes
 */
$di->set('flash', function() {
    $flash = new \Phalcon\Flash\Direct(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));

    return $flash;
});

// Flash Session using when u need redirect page
$di->set('flashSession', function() {
    $flashSession = new \Phalcon\Flash\Session(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));

    return $flashSession;
});

/**
 * Security
 */
$di->set('security', function() {
    $security = new \Phalcon\Security();
    //Set the password hashing factor to 12 rounds
    $security->setWorkFactor(12);

    return $security;
});

/**
 * Register Authentication service
 */
$di->set('auth', function() {
    return new \Jumpstart\Auth();
});

/**
 * Tag
 */
$di->set('tag', function() {
    return new \Phalcon\Tag();
});

/**
 * Models manager
 */
$di->set('modelsManager', function() {
    return new \Phalcon\Mvc\Model\Manager();
});

/**
 * Encrypt cookie
 */
$di->setShared('crypt', function() use ($config) {
    $crypt = new \Phalcon\Crypt();
    $crypt->setMode(MCRYPT_MODE_CFB);
    $crypt->setKey($config->encryptKey); //Use your own key!

    return $crypt;
});

/**
 * Cookie
 */
$di->setShared('cookie', function() {
    $cookie = new \Phalcon\Http\Response\Cookies();
    $cookie->useEncryption(true);

    return $cookie;
});

/**
 * Logger
 */
$di->set('logger', function() use ($di){
    return new \Phalcon\Logger\Adapter\Database('errors', array(
        'db' => $di->get('db'),
        'table' => 'logs'
    ));
});
