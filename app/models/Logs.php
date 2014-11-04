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

class Logs extends BaseModel 
{ 
	const LEVEL_EMERGENCY = 0; 
	const LEVEL_CRITICAL = 1; 
	const LEVEL_ALERT = 2; 
	const LEVEL_ERROR = 3; 
	const LEVEL_WARNING = 4; 
	const LEVEL_NOTICE = 5; 
	const LEVEL_INFO = 6; 
	const LEVEL_DEBUG = 7; 
	
	public function getSource()
	{
		return 'logs';
	}

	public function beforeCreate()
	{
		$this->created_at = date('Y-m-d H:i:s');
	
	}

	public function beforeUpdate()
	{
		
	}

	public function initialize()
	{
		
	}

	public static function getLogss($formData)
	{
		$conditions = '';
		$bind = [];

		if (!isset($formData['sorttype'])) {
			$sorttype = (string) 'DESC';
		} else {
			$sorttype = (string) $formData['sorttype'];
		}

		if (!isset($formData['sortby'])) {
			$sortby = (string) 'id';
		} elseif ($formData['sortby'] == 'type') {
			$sortby = (string) 'type';
		} elseif ($formData['sortby'] == 'createdat') {
			$sortby = (string) 'created_at';
		} else {
			$sortby = (string) $formData['sortby'];
		}

		if (isset($formData['keyword'])) {
			$conditions = (string) '(name LIKE "%' . $formData['keyword'] . '%") OR (content LIKE "%' . $formData['keyword'] . '%")';
		} else {
			$conditions = '';
		}

		$parameters = [
			'models' => ['Model\Logs'],
			'column' => ['*'],
			'conditions' => $conditions,
			'order' => ['Model\Logs.' . $sortby . ' ' . $sorttype . ''],
		];

		$builder = new \Phalcon\Mvc\Model\Query\Builder($parameters);
		return $builder;
	}

	public function getTypeName()
	{
		$name = '';

		switch ($this->type) {
			case self::LEVEL_EMERGENCY:
				$name = 'Emergency';
				break;
			case self::LEVEL_CRITICAL:
				$name = 'Critical';
				break;
			case self::LEVEL_ALERT:
				$name = 'Alert';
				break;
			case self::LEVEL_ERROR:
				$name = 'Error';
				break;
			case self::LEVEL_WARNING:
				$name = 'Warning';
				break;
			case self::LEVEL_NOTICE:
				$name = 'Notice';
				break;
			case self::LEVEL_INFO:
				$name = 'Info';
				break;
			case self::LEVEL_DEBUG:
				$name = 'Debug';
				break;
			
		}

		return $name;
	}

	public static function getTypeList()
	{
		$output = array();

		$output[self::LEVEL_EMERGENCY] = 'Emergency';
		$output[self::LEVEL_CRITICAL] = 'Critical';
		$output[self::LEVEL_ALERT] = 'Alert';
		$output[self::LEVEL_ERROR] = 'Error';
		$output[self::LEVEL_WARNING] = 'Warning';
		$output[self::LEVEL_NOTICE] = 'Notice';
		$output[self::LEVEL_INFO] = 'Info';
		$output[self::LEVEL_DEBUG] = 'Debug';
		
		return $output;
	}

	public function getTypeLabel()
	{
		$label = '';

		switch ($this->type) {
			case self::LEVEL_EMERGENCY:
				$label = 'danger';
				break;
			case self::LEVEL_CRITICAL:
				$label = 'danger';
				break;
			case self::LEVEL_ALERT:
				$label = 'warning';
				break;
			case self::LEVEL_ERROR:
				$label = 'danger';
				break;
			case self::LEVEL_WARNING:
				$label = 'warning';
				break;
			case self::LEVEL_NOTICE:
				$label = 'primary';
				break;
			case self::LEVEL_INFO:
				$label = 'info';
				break;
			case self::LEVEL_DEBUG:
				$label = 'default';
				break;
		}

		return $label;
	}

}
