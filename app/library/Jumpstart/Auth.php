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

/**
 * Authentication
 *
 */
class Auth extends \Phalcon\Mvc\User\Component
{
    /**
     * Checking user existing in system
     * 
     * @param  string  $email
     * @param  string  $password
     * @param  boolean $cookie
     * @return boolean
     */
    public function authentication($email, $password, $cookie = false)
    {
        $myUser = \Model\User::findFirst([
            'email = :femail:',
            'bind' =>   ['femail' => $email]
        ]);

        if ($myUser) {
            if ($this->security->checkHash($password, $myUser->password)) {

                // create session for user
                $this->session->set('me', $myUser);

                // store cookie if chosen
                if ($cookie == true) {
                    $this->cookie->set('remember-me', $myUser->id, time() + 15 * 86400);
                }

                // Store user logged in (LOG_IN::userId::userEmail::userAgent::ip)
                $this->logger->name = 'access'; // Your own log name
                $this->logger->info(    'LOG_IN::'
                                        . $myUser->id .'::'
                                        . $myUser->email .'::'
                                        . $this->request->getUserAgent() .'::'
                                        . $this->request->getClientAddress()
                                    );

                return true;
            } else {
                $this->flash->error('Password not match.');
            }
        } else {
            $this->flash->error('User not exist.');
        }
    }
}
