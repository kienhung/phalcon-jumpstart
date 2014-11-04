<?php

define('ROLE_GUEST', 1);
define('ROLE_ADMIN', 5);
define('ROLE_MOD', 10);
define('ROLE_MEMBER', 15);

$perm = [
	ROLE_GUEST =>  [
                        'Admin' => array (       //namespace
                            'index:index',      //controller:action
                            'login:*',
                        ),
                        'Site' => array (
                            'index:*',
                            'install:*',
                        ),
                    ],

    ROLE_ADMIN =>  [
                        'Admin' => array (       //namespace
	                        'index:*',          //controller:action
	                        'login:*',
	                        'dashboard:*',
	                        'generator:*',
	                        'logs:*',
	                        'user:*',
	                        'crontask:*',
	                        'profile:*',
	                    ),
	                    'Site' => array (
	                        'index:*',
	                        'user:*',
	                        'install:*',
	                    ),
                    ],

    ROLE_MOD 	=> 	[
                        'Admin' => array (       //namespace
                            'index:*',          //controller:action
                            'login:*',
                            'dashboard:*',
                            'user:*',
                            'profile:*',
                        ),
                        'Site' => array (
                            'index:*',
                            'user:*',
                        ),
                    ],
                    
    ROLE_MEMBER => [
                        'Admin' => array (       //namespace
                            'index:*',          //controller:action
                        ),
                        'Site' => array (
                            'index:*',
                            'user:*',
                        ),
                    ],
];
