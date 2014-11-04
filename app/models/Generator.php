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

class Generator extends BaseModel
{
    public static function getTypeName($value)
    {
        $myConstList = [];

        $refl = new \ReflectionClass('\Phalcon\Db\Column');
        $constList = $refl->getConstants();

        foreach ($constList as $constName => $constValue) {
            if (preg_match('/^TYPE_([A-Z])+$/', $constName)) {
                $myConstList[$constValue] = (string) $constName;
            }
        }

        return $myConstList[$value];
    }

}