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

namespace Model;

class BaseModel extends \Phalcon\Mvc\Model
{
	protected $di;

    public function onConstruct()
    {
        global $di;
        $this->di = $di;
    }

    public function initialize()
    {
        
    }

    public static function runQuery($object, $sql)
    {
        $result = new \Phalcon\Mvc\Model\Resultset\Simple(null, $object, $object->getReadConnection()->query($sql));

        return $result;
    }
}