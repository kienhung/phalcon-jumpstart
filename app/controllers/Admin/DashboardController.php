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

use Jumpstart\Helper;

class DashboardController extends BaseController
{
    public function indexAction()
    {
    	$formData = [];
    	$paginateUrl = $this->getControllerUrl();

        $serverPHP = $_SERVER['SERVER_SOFTWARE'];
        $pos = strripos($serverPHP, 'php');

        $formData['fserverip'] = $this->request->getServerAddress();
        $formData['fclientip'] = $this->request->getClientAddress();
        $formData['fserver'] = trim(substr($serverPHP, 0, $pos-1));
        $formData['fphp'] = trim(substr($serverPHP, $pos));
        $formData['fuseragent'] = $this->request->getUserAgent();
        $now = new \DateTime();
        $formData['now'] = $now->format('d/m/Y H:i:s');

        $this->tag->appendTitle('Index');
        $this->view->setVars([
            'formData'      => $formData,
            'redirectUrl'   => base64_encode(urlencode(Helper::getCurrentUrl()))
        ]);
    }
}