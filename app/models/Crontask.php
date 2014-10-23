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

class Crontask extends BaseModel 
{ 
	const STATUS_SUCCESS = 1; 
	const STATUS_PROCESSING = 3; 
	
	public function getSource()
	{
		return 'crontask';
	}

	public function beforeCreate()
	{
		$this->created_at = date('Y-m-d H:i:s');
	
	}

	public function beforeUpdate()
	{
		$this->created_at = date('Y-m-d H:i:s');
	}

	public function initialize()
	{
		
	}

	public static function getCrontasks($formData)
	{
		$conditions = '';

		if (!isset($formData['sorttype'])) {
			$sorttype = (string) 'DESC';
		} else {
			$sorttype = (string) $formData['sorttype'];
		}

		if (!isset($formData['sortby'])) {
			$sortby = (string) 'id';
		} elseif ($formData['sortby'] == 'timeprocessing') {
			$sortby = (string) 'timeprocessing';
		} elseif ($formData['sortby'] == 'status') {
			$sortby = (string) 'status';
		} elseif ($formData['sortby'] == 'createdat') {
			$sortby = (string) 'created_at';
		} else {
			$sortby = (string) $formData['sortby'];
		}

		if (isset($formData['keyword'])) {
			$conditions = (string) '(task LIKE "%' . $formData['keyword'] . '%") OR (action LIKE "%' . $formData['keyword'] . '%") OR (ipaddress LIKE "%' . $formData['keyword'] . '%") OR (output LIKE "%' . $formData['keyword'] . '%")';
		} else {
			$conditions = '';
		}

		$parameters = [
			'models' => ['Model\Crontask'],
			'column' => ['*'],
			'conditions' => $conditions,
			'order' => ['Model\Crontask.' . $sortby . ' ' . $sorttype . ''],
		];

		$builder = new \Phalcon\Mvc\Model\Query\Builder($parameters);
		return $builder;
	}

	public function getStatusName()
	{
		$name = '';

		switch ($this->status) {
			case self::STATUS_SUCCESS:
				$name = 'Success';
				break;
			case self::STATUS_PROCESSING:
				$name = 'PROCESSING';
				break;
			
		}

		return $name;
	}

	public static function getStatusList()
	{
		$output = array();

		$output[self::STATUS_SUCCESS] = 'Success';
		$output[self::STATUS_PROCESSING] = 'PROCESSING';
		
		return $output;
	}

	public function getStatusLabel()
    {
        $label = '';

        switch ($this->status) {
            case self::STATUS_SUCCESS:
                $label = 'primary';
                break;
            case self::STATUS_PROCESSING:
                $label = 'default';
                break;
        }

        return $label;
    }


}
