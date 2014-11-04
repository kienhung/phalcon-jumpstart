<?php

namespace Jumpstart;

use \Other\PrettyExceptions;
use \Phalcon\Mvc\Dispatcher\Exception as DispatchException;

class MyException extends PrettyExceptions
{
    protected $p;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->p = new PrettyExceptions();

    }

    public function handleException()
    {
        //Change the base uri for static resources
        $this->p->setBaseUri($this->config->application->baseUri. 'public/plugins/prettyexceptions/');

        //Set if the backtrace must be shown
        $this->p->showBacktrace(true);

        //Set whether if open the user files and show its code
        $this->p->showFiles(true);

        //Set whether show the complete file or just the relevant fragment
        $this->p->showFileFragment(true);

        /**
         * Set whether show human readable dump of current Phalcon application instance
         *  Can optionally pass a Phalcon application instance as a prameter in the
         *  constructor, or as the last parameter of PrettyExceptions::handle() and
         *  PrettyExceptions::handleError()
         */
        $this->p->showApplicationDump(true);

        //Change the CSS theme (default, night or minimalist)
        $this->p->setTheme('default');

        //Handle the error/exception
        set_exception_handler(function($e) {
            return $this->p->handle($e);
        });

        // set_error_handler(function($errorCode, $errorMessage, $errorFile, $errorLine) {
        //     return $this->p->handleError($errorCode, $errorMessage, $errorFile, $errorLine);
        // });
    }
}
