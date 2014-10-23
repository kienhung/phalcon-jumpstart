<?php
/**
 * Defined useful constant
 */
date_default_timezone_set('Asia/Bangkok');
define('DEBUG', true);
define('ROOT_PATH', realpath('.'));
define('DS', DIRECTORY_SEPARATOR);

/**
 * Stage developement Flag
 */
if(DEBUG == true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
    error_reporting(0);
}

/**
 * Versions Required.
 */
define('PHALCONJUMPSTART_VERSION', '0.1');
define('PHALCON_VERSION_REQUIRED', '1.3');
define('PHP_VERSION_REQUIRED', '5.4');

/**
 * Check phalcon framework installation.
 */
if (!extension_loaded('phalcon')) {
    printf('Install Phalcon framework %s', PHALCON_VERSION_REQUIRED);
    exit(1);
}

ob_start();

/**
 * Read the configuration
 */
require_once ROOT_PATH . '/app/config/config.php';

/**
 * Using the CLI factory default services container
 */
$di = new \Phalcon\DI\FactoryDefault\CLI();

/**
 * Read the settings
 */
require_once ROOT_PATH . '/app/config/setting.php';

/**
 * Register autoload
 */
require_once ROOT_PATH . '/app/config/loader.php';

/**
 * Shared global $config
 */
$di->setShared('config', function() use ($config) {
    return $config;
});

/**
 * Shared global $setting
 */
$di->setShared('setting', function() use ($setting) {
    return $setting;
});

/**
 * Set MySQL DB service
 */
$di->setShared('db', function() use ($config) {
    $db = new \Phalcon\Db\Adapter\Pdo\Mysql([
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ]);
    return $db;
});

/**
 * Set Mongo DB service
 */
$di->set('collectionManager', function(){
    return new \Phalcon\Mvc\Collection\Manager();
}, true);

$di->setShared('mongo', function() use ($config) {
    $mongo = new \MongoClient();
    return $mongo->selectDB("myapp");
});

/**
 * Create a console application
 */
 $console = new \Phalcon\CLI\Console();
 $console->setDI($di);

 /**
 * Process the console arguments
 */
 $arguments = array();
 foreach($argv as $k => $arg) {
     if($k == 1) {
         $arguments['task'] = $arg;
     } elseif($k == 2) {
         $arguments['action'] = $arg;
     } elseif($k >= 3) {
        $arguments['params'][] = $arg;
     }
 }

/**
 * define global constants for the current task and action
 */
 define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
 define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

 try {
    /**
     * handle incoming arguments
     */
    $console->handle($arguments);
     
 }
 catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
 }