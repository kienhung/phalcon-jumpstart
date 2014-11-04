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

class MainTask extends \Phalcon\CLI\Task
{
	protected $crontask;
	protected $timer;
	protected $cronId = 0;

	public function __construct()
	{
		set_time_limit(3600);

		$taskName = $this->router->getTaskName();
    	$actionName = $this->router->getActionName();

    	/**
    	 * Log processing status in logs table
    	 */
    	if (strlen($taskName) > 0 && strlen($actionName) > 0) {
    		$this->crontask = new \Model\Crontask();
    		$this->crontask->task = (string) $taskName;
    		$this->crontask->action = (string) $actionName;
    		$this->crontask->status = \Model\Crontask::STATUS_PROCESSING;
    		$this->crontask->ipaddress = $this->request->getServerAddress();
			$this->cronId = $this->crontask->create();
			
			$this->timer = new \Other\Timer();
			$this->timer->start();
    		
    	}
	}

    public function mainAction() {
         
    }

    public function __destruct()
    {
    	$this->timer->stop();
		
		/**
    	 * Log processing status in logs table
    	 */
		if($this->cronId > 0)
		{
			$this->crontask->timeprocessing = $this->timer->get_exec_time();
			$this->crontask->output = ob_get_contents();
			$this->crontask->status = \Model\Crontask::STATUS_SUCCESS;
			$this->crontask->update();
		}
		
     	ob_end_clean();
    }
}