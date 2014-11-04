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

$config =  new \Phalcon\Config(array(
    'uniqueId'              =>  'pj-de41dc25-d996-4c40-9818-61af1c375629', //http://www.guidgenerator.com/online-guid-generator.aspx
    'encryptKey'            =>  'BO1{2425z[-Yc)-',  //http://randomkeygen.com/

    'site' => array(
        'name'      => 'Phalcon Jumpstart',
        'url'       => 'http://phalconjumpstart.com',
        'project'   => 'PhalconJumpstart',
        'repo'      => 'https://github.com/nguyenducduy.it/phalcon-jumpstart',
        'docs'      => 'http://phalconjumpstart.com/docs',
    ),

    'database' => array(
        'host'              =>  'localhost',
        'username'          =>  'root',
        'password'          =>  'root',
        'dbname'            =>  'pj',
    ),

    'application' => array(
        'controllersDir'    =>  ROOT_PATH . '/app/controllers/',
        'tasksDir'          =>  ROOT_PATH . '/app/controllers/tasks/',
        'modelsDir'         =>  ROOT_PATH . '/app/models/',
        'viewsDir'          =>  ROOT_PATH . '/app/views/',
        'cacheDir'          =>  ROOT_PATH . '/app/cache/',
        'pluginsDir'        =>  ROOT_PATH . '/app/plugins/',
        'libraryDir'        =>  ROOT_PATH . '/app/library/',
        'languageDir'       =>  ROOT_PATH . '/app/language/',

        'baseUri'           =>  'http://localhost/phalcon-jumpstart/trunk/', //end slash is IMPORTANT
        'baseUriAdmin'      =>  'http://localhost/phalcon-jumpstart/trunk/admin/', //end slash is IMPORTANT
    ),
));
