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

class LoginController extends BaseController
{
    public function indexAction()
    {
        $redirectUrl = $this->dispatcher->getParam('redirect');
        $formData = array();
        $cookie = false;

        if (!empty($_POST['fsubmit'])) {
            if ($this->security->checkToken()) {
                $formData = array_merge($formData, $_POST);

                if (isset($formData['fcookie']) && $formData['fcookie'] == 'remember-me') {
                    $cookie = (boolean) true;
                }
                
                $identity = $this->auth->authentication($formData['femail'], $formData['fpassword'], $cookie);

                if ($identity == true) {
                    if ($redirectUrl != null) {
                        header('location: '. $redirectUrl .'');
                    } else {
                        $this->response->redirect('admin/dashboard');
                    }
                }

            } else {
                $this->flash->error('Token missmatch.');
            }
        }

        $this->tag->appendTitle('Authentication');
        $this->view->setVars([
            'formData' => $formData,
        ]);
    }
}
