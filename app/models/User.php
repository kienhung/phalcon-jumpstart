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

use \Jumpstart\Permission;

class User extends BaseModel
{
    const STATUS_ENABLE = 3;
    const STATUS_DISABLE = 5;

    public function getSource()
    {
        return 'user';
    }

    public function beforeCreate()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }
    
    public function initialize()
    {

    }

    /**
     * Select the record, Interface with the outside (Controller Action)
     *
     * @param array $formData : filter array to build WHERE condition
     * @param string $sortby : indicating the order of select
     * @param string $sorttype : DESC or ASC
     */
    public static function getUsers($formData)
    {
        $conditions = '';

        // DESC OR ASC
        if (!isset($formData['sorttype'])) {
            $sorttype = (string) 'DESC';
        } else {
            $sorttype = (string) $formData['sorttype'];
        }

        // ORDER BY
        if (!isset($formData['sortby'])) {
            $sortby = (string) 'id';
        } elseif ($formData['sortby'] == 'datecreated') {
            $sortby = (string) 'created_at';
        } else {
            $sortby = (string) $formData['sortby'];
        }

        // LIKE
        if (isset($formData['keyword'])) {
            $conditions = '(name LIKE \'%'. $formData['keyword'] .'%\')
                            OR (email LIKE \'%'. $formData['keyword'] .'%\')';
        } else {
            $conditions = '';
        }

        $parameters = [
            'models' => ['Model\User'],
            'columns' => ['*'],
            'conditions' => $conditions,
            'order' => ['Model\User.'. $sortby .' '. $sorttype .'']
        ];

        $builder = new \Phalcon\Mvc\Model\Query\Builder($parameters);

        return $builder;
    }

    public static function findFirst($parameters=null)
    {
        $data = parent::findFirst($parameters);
        return $data;
    }

    public function getStatusName()
    {
        $name = '';

        switch ($this->status) {
            case self::STATUS_ENABLE:
                $name = 'enable';
                break;
            case self::STATUS_DISABLE:
                $name = 'disable';
                break;
        }

        return $name;
    }

    public static function getStatusList()
    {
        $output = array();

        $output[self::STATUS_ENABLE] = 'Enable';
        $output[self::STATUS_DISABLE] = 'Disable';


        return $output;
    }

    public function getStatusLabel()
    {
        $label = '';

        switch ($this->status) {
            case self::STATUS_ENABLE:
                $label = 'primary';
                break;
            case self::STATUS_DISABLE:
                $label = 'danger';
                break;
        }

        return $label;
    }

    public function getRoleLabel()
    {
        $label = '';

        switch ($this->role) {
            case ROLE_ADMIN:
                $label = 'warning';
                break;
            case ROLE_MOD:
                $label = 'info';
                break;
            case ROLE_MEMBER:
                $label = 'default';
                break;
        }

        return $label;
    }

    public function getRoleName()
    {
        $name = '';

        switch ($this->role) {
            case ROLE_ADMIN:
                $name = 'administrator';
                break;
            case ROLE_MOD:
                $name = 'moderator';
                break;
            case ROLE_MEMBER:
                $name = 'member';
                break;
        }

        return $name;
    }

    public static function getRoleList()
    {
        $output = array();

        $output[ROLE_ADMIN] = 'Administrator';
        $output[ROLE_MOD] = 'Moderator';
        $output[ROLE_MEMBER] = 'Member';


        return $output;
    }

    public function deleteImage($imagepath = '')
    {
        global $config;
        global $setting;
        
        //delete current image
        if($imagepath == '')
            $deletefile = $this->avatar;
        else
            $deletefile = $imagepath;
        
        if(strlen($deletefile) > 0) {
            $file = $setting->user->imageDirectory . $deletefile;
            if(file_exists($file) && is_file($file))
            {
                @unlink($file);
                
                //get small image name
                $pos = strrpos($deletefile, '.');
                $extPart = substr($deletefile, $pos+1);
                $namePart =  substr($deletefile,0, $pos);
                
                $deletesmallimage = $namePart . '-small.' . $extPart;
                $file = $setting->user->imageDirectory . $deletesmallimage;
                if(file_exists($file) && is_file($file))
                    @unlink($file);
    
                $deletemediumimage = $namePart . '-medium.' . $extPart;
                $file = $setting->user->imageDirectory . $deletemediumimage;
                if(file_exists($file) && is_file($file))
                    @unlink($file);
            }
            
            //delete current image
            if($imagepath == '')
                $this->avatar = '';
        }
    }


    public function getSmallImage()
    {
        global $config;
        global $setting;

        $avatar = !empty($this->avatar) ? $this->avatar : 'noavatar.png';

        $pos = strrpos($avatar, '.');
        $extPart = substr($avatar, $pos+1);
        $namePart =  substr($avatar,0, $pos);
        $filesmall = $namePart . '-small.' . $extPart;  
        
        $url = $config->application->baseUri . $setting->user->viewUrl . $filesmall;     
        return $url;
    }
    
    public function getMediumImage()
    {
        global $config;
        global $setting;
        
        $avatar = !empty($this->avatar) ? $this->avatar : 'noavatar.png';

        $pos = strrpos($avatar, '.');
        $extPart = substr($avatar, $pos+1);
        $namePart =  substr($avatar,0, $pos);
        $filemedium = $namePart . '-medium.' . $extPart;    
        
        $url = $config->application->baseUri . $setting->user->viewUrl . $filemedium;        
        return $url;
    }
    
    public function getImage()
    {
        global $config;
        global $setting;
        
        $avatar = !empty($this->avatar) ? $this->avatar : 'noavatar.png';

        $url = $config->application->baseUri . $setting->user->viewUrl . $avatar;

        return $url;    
    }
}