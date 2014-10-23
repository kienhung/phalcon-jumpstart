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
 * Defined useful constant
 */
date_default_timezone_set('Asia/Bangkok');
define('DEBUG', true);
define('ROOT_PATH', realpath('..'));
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

/**
 * Read the configuration
 */
require_once ROOT_PATH . '/app/config/config.php';

/**
 * Read the settings
 */
require_once ROOT_PATH . '/app/config/setting.php';

/**
 * Read the permission
 */
require_once ROOT_PATH . '/app/config/permission.php';

/**
 * Register autoload
 */
require_once ROOT_PATH . '/app/config/loader.php';

/**
 * Register DI service
 */
require_once ROOT_PATH . '/app/config/service.php';

/**
 * Start Ubench profiler
 */
if (isset($_GET['uprofiler'])) {
    $bench = new \Other\Ubench();
    $bench->start();
}

/**
 * Register a Exception handler
 * @var [type]
 */
$myException = new \Jumpstart\MyException($config);
$myException->handleException();

/**
 * Start Application
 * @var [type]
 */
$application = new \Phalcon\Mvc\Application($di);
echo $application->handle()->getContent();

/**
 * End Ubench Profiler
 */
if (isset($_GET['uprofiler'])) {
    $bench->end();
    require_once ROOT_PATH . '/public/benchTemplate.phtml';
}
