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

$router = new \Phalcon\Mvc\Router(false);

/**
 * Route to Sub Controller SITE
 */
$router->add('/:controller/:action/:params', array(
    'namespace' => 'Controller\Site',
    'controller' => 1,
    'action' => 2,
    'params' => 3,
));

$router->add('/:controller', array(
    'namespace' => 'Controller\Site',
    'controller' => 1
));

$router->add('/', array(
    'namespace' => 'Controller\Site',
    'controller' => 'index',
    'action' => 'index'
));

/**
 * Route to Sub Controller ADMIN
 */
$router->add('/admin/:controller/:action/:params', array(
    'namespace' => 'Controller\Admin',
    'controller' => 1,
    'action' => 2,
    'params' => 3,
));

$router->add('/admin/:controller', array(
    'namespace' => 'Controller\Admin',
    'controller' => 1
));

$router->add('/admin', array(
    'namespace' => 'Controller\Admin',
    'controller' => 'index',
    'action' => 'index'
));

$router->add('/admin/logout', array(
    'namespace' => 'Controller\Admin',
    'controller' => 'index',
    'action' => 'logout'
));

/**
 * Remove trailing slashes automatic
 */
$router->removeExtraSlashes(true);

return $router;
