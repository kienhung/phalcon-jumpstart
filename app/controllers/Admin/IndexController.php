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

namespace Controller\Admin;

use \Jumpstart\Permission;

class IndexController extends BaseController
{
    public function indexAction()
    {
        if (isset($_GET['rebuild'])) {
            $perm = new Permission($this->perm);
            $perm->rebuild();
        }

        if ($this->session->has('me') == false) {
            return $this->dispatcher->forward([
                'namespace' => 'Controller\Admin',
                'controller' => 'login',
                'action' => 'index'
            ]);
        } else {
            $this->dispatcher->forward([
                'namespace' => 'Controller\Admin',
                'controller' => 'dashboard',
                'action' => 'index'
            ]);
        }
    }

    public function logoutAction()
    {
        // Store user logged out (LOG_OUT::userId::userEmail::userAgent::ip)
        $this->logger->name = 'access'; // Your own log name
        $this->logger->info(    'LOG_OUT::' .
                                $this->session->get('me')->id .'::'
                                . $this->session->get('me')->email .'::'
                                . $this->request->getUserAgent() .'::'
                                . $this->request->getClientAddress()
                            );

        // remove session
        $this->session->destroy();

        // delete cookie
        if ($this->cookie->has('remember-me')) {
            $this->cookie->delete('remember-me');
        }

        $this->response->redirect('admin/');
    }
}
