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
use Other\Uploader;
use Other\ImageResizer;

class ProfileController extends BaseController
{
	public function indexAction()
    {
    	$formData = $error = $success = [];    	

    	$me = $this->session->get('me');

    	if (!empty($_POST['fsubmit'])) {
    		if ($this->security->checkToken()) {
    			$formData = array_merge($formData, $_POST);

    			if ($this->editActionValidator($formData, $error)) {
    				$me->name = $formData['fname'];

    				if ($me->update()) {
                        // Upload Image Process
                        if (strlen($_FILES['favatar']['name']) > 0) {
                            $this->uploadImage($me);
                        }

                        $this->flash->success($this->lang->_('SuccessUpdateData'));
                    } else {
                        $this->flash->error($this->lang->_('ErrorUpdateData'));
                    }
    			} else {
                    $messageList = '';
                    foreach ($error as $errName => $errMessage) {
                        $messageList .= $errMessage . '<br/>';
                    }
                    $this->flash->error($messageList);
                }
    		} else {
                $this->flash->error($this->lang->_('ErrorTokenMissMatch'));
            }
    	}

        $this->session->set('me', $me);

    	$this->tag->appendTitle($me->name);
        $this->view->setVars([
        	'me'			=> $me,
            'formData'      => $formData,
            'redirectUrl'   => base64_encode(urlencode(Helper::getCurrentUrl()))
        ]);
    }

    public function changePasswordAction()
    {
        $formData = $error = $success = [];     

        $me = $this->session->get('me');

        if (!empty($_POST['fsubmit'])) {
            if ($this->security->checkToken()) {
                $formData = array_merge($formData, $_POST);

                if ($this->changepasswordActionValidator($formData, $error)) {
                    $me->password = (string) $this->security->hash($formData['fnewpassword']);

                    if ($me->update()) {
                        $this->flash->success($this->lang->_('SuccessChangePassword'));
                    } else {
                        $this->flash->error($this->lang->_('ErrorChangePassword'));
                    }
                } else {
                    $messageList = '';
                    foreach ($error as $errName => $errMessage) {
                        $messageList .= $errMessage . '<br/>';
                    }
                    $this->flash->error($messageList);
                }
            } else {
                $this->flash->error($this->lang->_('ErrorTokenMissMatch'));
            }
        }

        $this->session->set('me', $me);
    }

    public function changepasswordActionValidator($formData, &$error)
    {
        $pass = true;

        $me = $this->session->get('me');

        if ($formData['foldpassword'] == '') {
            $error['OldPasswordIsRequired'] = $this->lang->_('OldPasswordIsRequired');
            $pass = false;
        } else {
            if (!$this->security->checkHash($formData['foldpassword'], $me->password)) {
                $error['PasswordNotMatch'] = $this->lang->_('PasswordNotMatch');
                $pass = false;
            }
        }

        if ($formData['fnewpassword'] == '') {
            $error['NewPasswordIsRequired'] = $this->lang->_('NewPasswordIsRequired');
            $pass = false;
        } else {
            if ($formData['fnewpassword'] != $formData['fconfirmpassword']) {
                $error['NewPasswordNotMatch'] = $this->lang->_('NewPasswordNotMatch');
                $pass = false;
            }
        }

        return $pass;
    }

    public function editActionValidator($formData, &$error) {
        $pass = true;

        if ($formData['fname'] == '') {
        	$error['NameIsRequired'] = $this->lang->_('NameIsRequired');
            $pass = false;
        }

        if(strlen($_FILES['favatar']['name']) > 0)
        {
            //check extension
            $ext = strtoupper(Helper::fileExtension($_FILES['favatar']['name']));

            if(!in_array($ext, $this->setting->user->validExtension->toArray()))
            {
                $error['FileTypeNotValid'] = 'File type invalid';
                $pass = false;
            }
            
            if($_FILES['favatar']['size'] > $this->setting->user->validMaxFileSize)
            {
                $error['errFileSizeNotValid'] = 'File size invalid';
                $pass = false;
            }
        }

        return $pass;
    }

    public function uploadImage($me)
    {
        $curDateDir = Helper::getCurrentDateDirName(); 
        $extPart = substr(strrchr($_FILES['favatar']['name'],'.'),1);
        $namePart =  Helper::codau2khongdau($me->name, true) . '-' . $me->id . time();
        $name = $namePart . '.' . $extPart;

        $uploader = new Uploader($_FILES['favatar']['tmp_name'], $name, $this->setting->user->imageDirectory . $curDateDir, '');
        
        $uploadError = $uploader->upload(false, $name);
       
        if($uploadError != Uploader::ERROR_UPLOAD_OK) {
            return $uploadError;
        } else {
            //Resize big image if needed
            $myImageResizer = new ImageResizer( $this->setting->user->imageDirectory . $curDateDir, $name, 
                                                $this->setting->user->imageDirectory . $curDateDir, $name, 
                                                $this->setting->user->imageMaxWidth, 
                                                $this->setting->user->imageMaxHeight, 
                                                '', 
                                                $this->setting->user->imageQuality);
            $myImageResizer->output();    
            unset($myImageResizer);
            
            //Create medium image
            $nameMediumPart = substr($name, 0, strrpos($name, '.'));
            $nameMedium = $nameMediumPart . '-medium.' . $extPart;
            $myImageResizer = new ImageResizer( $this->setting->user->imageDirectory . $curDateDir, $name, 
                                                $this->setting->user->imageDirectory . $curDateDir, $nameMedium, 
                                                $this->setting->user->imageMediumWidth, 
                                                $this->setting->user->imageMediumHeight, 
                                                '', 
                                                $this->setting->user->imageQuality);
            $myImageResizer->output();    
            unset($myImageResizer);

            //Create thum image
            $nameThumbPart = substr($name, 0, strrpos($name, '.'));
            $nameThumb = $nameThumbPart . '-small.' . $extPart;
            $myImageResizer = new ImageResizer( $this->setting->user->imageDirectory . $curDateDir, $name, 
                                                $this->setting->user->imageDirectory . $curDateDir, $nameThumb, 
                                                $this->setting->user->imageThumbWidth, 
                                                $this->setting->user->imageThumbHeight, 
                                                $this->setting->user->imageThumbRatio, 
                                                $this->setting->user->imageQuality);
            $myImageResizer->output();    
            unset($myImageResizer);

            //update database                
            $filepath = $curDateDir . $name;

            $oldavatar = $me->avatar;
            $me->avatar = (string) $filepath;
            if ($me->save()) {

            	$pos = strrpos($oldavatar, '.');
		        $extPart = substr($oldavatar, $pos+1);
		        $namePart =  substr($oldavatar,0, $pos);
		        $filesmall = $namePart . '-small.' . $extPart;
		        $filemedium = $namePart . '-medium.' . $extPart;

            	$largeAvatar = $this->setting->user->imageDirectory . $oldavatar;
            	$mediumAvatar = $this->setting->user->imageDirectory . $filemedium;
            	$smallAvatar = $this->setting->user->imageDirectory . $filesmall;

            	if (is_file($largeAvatar)) {
            		@unlink($largeAvatar);
            	}
            	if (is_file($mediumAvatar)) {
            		@unlink($mediumAvatar);
            	}
            	if (is_file($smallAvatar)) {
            		@unlink($smallAvatar);
            	}
            }
        }
    }
}