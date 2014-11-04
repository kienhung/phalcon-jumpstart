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

$loader = new \Phalcon\Loader();

/**
 * Registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
    'Controller'    =>  $config->application->controllersDir,
    'Model'         =>  $config->application->modelsDir,
    'Phalcon'       =>  $config->application->libraryDir . 'Phalcon/',
    'Jumpstart'     =>  $config->application->libraryDir . 'Jumpstart/',
    'Other'         =>  $config->application->libraryDir . 'Other/',
]);

/**
 * Registering a set of tasks
 */
$loader->registerDirs([
    $config->application->tasksDir,
]);

$loader->register();