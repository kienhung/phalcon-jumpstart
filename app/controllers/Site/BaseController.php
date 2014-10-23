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

namespace Controller\Site;

class BaseController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {   
        $this->tag->setTitle('Phalcon JumpStart &raquo; ');
    }

    public function beforeExecuteRoute()
    {
        /**
         * Register language translator service
         */
        $this->di->setShared('lang', function() {
            $language = '';
            // Detect language via cookie
            if ($this->cookie->has('language')) {
                $this->cookie->useEncryption(false);
                $cookieLang = $this->cookie->get('language');
                $language = (string) $cookieLang->getValue();
            } else {
                $language = $this->setting->global->defaultLanguage;
            }

            $directoryLangPath = $this->config->application->languageDir;
            $currentController = $this->router->getControllerName();
            $currentNamespace = $this->router->getNamespaceName();
            return new \Phalcon\Translate\Adapter\Mynative([
                'dirLang' => $directoryLangPath,
                'namespace' => strtolower(substr($currentNamespace, strpos($currentNamespace, '\\') + 1)),
                'controller' => $currentController,
                'language' => $language
            ]);
        });

        $this->view->setVar('lang', $this->di->get('lang'));
    }

    public function afterExecuteRoute()
    {
        $this->view->setViewsDir($this->view->getViewsDir() . 'site/');
        $this->view->setTemplateAfter('main');  //extends from sub controller layouts directory, file main.volt
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);  //render within template layouts directory
    }

    public function getControllerUrl()
    {
        $url = ltrim($this->router->getRewriteUri(), '/');
        return $url;
    }
}