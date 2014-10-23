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

namespace Jumpstart;

use Jumpstart\Helper;

/**
 * Authorization class
 */
class Permission extends \Phalcon\Mvc\User\Plugin
{
    protected $permission =  [];

    public function __construct($perm = array())
    {
        // Init permission array
        // Write in config/permission.php
        $this->permission = $perm;
    }

    private function _getAcl()
    {
        $aclKey = 'acl-' . $this->config->uniqueId;

        if (extension_loaded('apc') && ini_get('apc.enabled') && apc_exists($aclKey)) {
            $acl = apc_fetch($aclKey);
        } else {
            $groupList = array_keys($this->permission);

            $acl = new \Phalcon\Acl\Adapter\Memory();
            $acl->setDefaultAction(\Phalcon\Acl::DENY);

            foreach ($groupList as $groupConst => $groupValue) {
                // Add Role
                $acl->addRole(new \Phalcon\Acl\Role($groupValue));

                if (isset($this->permission[$groupValue]) && is_array($this->permission[$groupValue]) == true) {
                    foreach ($this->permission[$groupValue] as $group => $controller) {
                        foreach ($controller as $action) {
                            $actionArr = explode(':', $action);
                            $resource = 'Controller\\' . $group . '-' . $actionArr[0];

                            // Add Resource
                            $acl->addResource($resource, $actionArr[1]);

                            // Grant role to resource
                            $acl->allow($groupValue, $resource, $actionArr[1]);
                        }
                    }
                }
            }


            // Store in APC
            if (extension_loaded('apc') && ini_get('apc.enabled')) {
                if (apc_exists($aclKey)) {
                    apc_delete($aclKey);
                }

                apc_store($aclKey, $acl, 0);
            }
        }

        return $acl;
    }

    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $me = null;

        // check exsited cookie
        if ($this->cookie->has('remember-me')) {
            $rememberMe = $this->cookie->get('remember-me');
            $userId = $rememberMe->getValue();
            $myUser = \Model\User::findFirstById((int) $userId);

            $this->session->set('me', $myUser);
            $role = $myUser->role;
        } else {
            //Get role name from session
            if ($this->session->has('me')) {
                $me = $this->session->get('me');
                $role = $me->role;
            } else {
                $role = ROLE_GUEST;
            }
        }

        $currentNamespace = $dispatcher->getNamespaceName();
        $currentController = $dispatcher->getControllerName();
        $current_resource = $currentNamespace . '-' . $currentController;
        $current_action = $dispatcher->getActionName();

        $acl = $this->_getAcl();

        $allowed = $acl->isAllowed($role, $current_resource, $current_action);

        // khong co quyen + chua dang nhap
        if ($allowed != \Phalcon\Acl::ALLOW && $me == null) {
            return $this->dispatcher->forward([
                'namepsace' => $currentNamespace,
                'controller' => 'login',
                'action' => 'index',
                'params' => ['redirect' => Helper::getCurrentUrl()],
            ]);
        } elseif ($allowed != \Phalcon\Acl::ALLOW && $me->id > 0) {
            // khong co quyen + dang nhap roi
            return $this->dispatcher->forward([
                'namepsace' => 'Controller\Site',
                'controller' => 'index',
                'action' => 'notfound',
            ]);
        }

    }

    /**
     * Using to generate acl object when update role or feature
     * 
     * @return object $acl
     */
    public function rebuild()
    {
        $aclKey = 'acl-' . $this->config->uniqueId;

        // Get all constant of this class
        $groupList = array_keys($this->permission);

        $acl = new \Phalcon\Acl\Adapter\Memory();
        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        foreach ($groupList as $groupConst => $groupValue) {
            // Add Role
            $acl->addRole(new \Phalcon\Acl\Role($groupValue));

            if (isset($this->permission[$groupValue]) && is_array($this->permission[$groupValue]) == true) {
                foreach ($this->permission[$groupValue] as $group => $controller) {
                    foreach ($controller as $action) {
                        $actionArr = explode(':', $action);
                        $resource = 'Controller\\' . $group . '-' . $actionArr[0];

                        // Add Resource
                        $acl->addResource($resource, $actionArr[1]);

                        // Grant role to resource
                        $acl->allow($groupValue, $resource, $actionArr[1]);
                    }
                }
            }
        }

        // Store in APC
        if (extension_loaded('apc') && ini_get('apc.enabled')) {
            if (apc_exists($aclKey)) {
                if (apc_delete($aclKey)) {
                    echo 'Delete old ACL in APC storage. <br/>';
                }
            }

            if (apc_store($aclKey, $acl, 0)) {
                echo 'Store new ACL in APC storage SUCCESS. <hr/>';
            }
        }
    }

}
