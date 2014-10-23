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

class UserController extends BaseController
{
    /**
     * define record show on per page
     * 
     * @var integer
     */
    protected $recordPerPage = 30;

    /**
     * User listing
     */
    public function indexAction()
    {
        $formData = [];
        $paginateUrl = $this->getControllerUrl();

        $page = $this->request->getQuery('page', 'int', 1);

        if (!empty($_POST['fsubmitbulk'])) {
            if (!isset($_POST['fbulkid'])) {
                $this->flash->warning('No bulk item selected.');
            } else {
                if ($this->security->checkToken()) {
                    $formData['fbulkid'] = $_POST['fbulkid'];

                    if ($_POST['fbulkaction'] == 'delete') {
                        $deletearr = $_POST['fbulkid'];

                        // Start a transaction
                        $this->db->begin();
                        $successId = [];

                        foreach ($deletearr as $deleteid) {
                            $myUser = \Model\User::findFirst(['id = :id:', 'bind' => ['id' => (int) $deleteid]])->delete();

                            // If fail stop a transaction
                            if ($myUser == false) {
                                $this->db->rollback();
                                return;
                            } else {
                                $successId[] = $deleteid;
                            }
                        }
                        // Commit a transaction
                        if ($this->db->commit() == true) {
                            $this->flash->success('User with ID: <strong>'. implode(', ', $successId) .'</strong> is deleted successfully.');

                            $formData['fbulkid'] = null;
                        } else {
                            $this->flash->error('Have a problem in deleting process. Try again later.');
                        }
                    } else {
                        $this->flash->warning('No action selected.');
                    }
                } else {
                    $this->flash->error('Token missmatch.');
                }
            }
        }

        /**
         * Check sort column condition
         */
        if (isset($_GET['sortby'])) {
            $formData['sortby'] = (string) $_GET['sortby'];
        } else {
            $formData['sortby'] = (string) 'id';
        }
        $paginateUrl .= '?sortby=' . $formData['sortby'];

        /**
         * Check sort column condition
         */
        if (isset($_GET['sorttype'])) {
            $formData['sorttype'] = (string) $_GET['sorttype'];
        } else {
            $formData['sorttype'] = (string) 'DESC';
        }
        $paginateUrl .= '&sorttype=' . $formData['sorttype'];

        /**
         * Check search column define
         */
        if (isset($_GET['keyword'])) {
            $formData['keyword'] = (string) $_GET['keyword'];
            $paginateUrl .= '&keyword=' . $formData['keyword'];
        }

        $myBuilder = \Model\User::getUsers($formData);

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder([
            'builder' => $myBuilder,
            'limit' => $this->recordPerPage,
            'page' => $page
        ]);

        $this->tag->appendTitle('User Listing');
        $this->view->page = $paginator->getPaginate();
        $this->view->setVars([
            'formData'      => $formData,
            'redirectUrl'   => base64_encode(urlencode(Helper::getCurrentUrl())),
            'paginateUrl'   => $paginateUrl,
        ]);
    }

    /**
     * Create a new user
     */
    public function addAction()
    {
        $formData = $error = [];

        if (!empty($_POST['fsubmit'])) {
            if ($this->security->checkToken()) {
                $formData = array_merge($formData, $_POST);

                if ($this->addActionValidator($formData, $error)) {
                    $myUser = new \Model\User();
                    $myUser->name = (string) $formData['fname'];
                    $myUser->password = (string) $this->security->hash($formData['fpassword']);
                    $myUser->email = (string) $formData['femail'];
                    $myUser->role = (string) $formData['frole'];
                    $myUser->status = (int) $formData['fstatus'];

                    if ($myUser->create()) {
                        // Upload Image Process
                        if (strlen($_FILES['favatar']['name']) > 0) {
                            $this->uploadImage($myUser->name, $myUser->id);
                        }

                        $this->flash->success('User: <strong>'. $myUser->name .'</strong> created successfully.');
                    } else {
                        $this->flash->error($this->lang->_('AdminPostErrorInsertData'));
                    }
                } else {
                    $messageList = '';
                    foreach ($error as $errName => $errMessage) {
                        $messageList .= $errMessage . '<br/>';
                    }
                    $this->message = $messageList;
                    $this->flash->error($messageList);
                }
            } else {
                $this->flash->error('Token missmatch. Re enter page from browser toolbar and try again later.');
            }
        }

        $this->tag->appendTitle('Add New User');
        $this->view->setVars([
            'formData' => $formData,
            'statusList' => \Model\User::getStatusList(),
            'roleList' => \Model\User::getRoleList(),
        ]);
    }

    /**
     * Edit existed user
     */
    public function editAction($id)
    {
        $formData = $error = [];
        $id = (int) $id;

        $myUser = \Model\User::findFirst(['id = :id:', 'bind' => ['id' => (int) $id]]);
        $formData['fname'] = $myUser->name;
        $formData['femail'] = $myUser->email;
        $formData['frole'] = $myUser->role;
        $formData['fstatus'] = $myUser->status;

        if (!empty($_POST['fsubmit'])) {
            if ($this->security->checkToken()) {
                $formData = array_merge($formData, $_POST);

                if ($this->editActionValidator($formData, $error)) {
                    $myUser = \Model\User::findFirst(['id = :id:', 'bind' => ['id' => (int) $id]]);

                    $myUser->name = (string) $formData['fname'];
                    $myUser->email = (string) $formData['femail'];
                    $myUser->role = (string) $formData['frole'];
                    $myUser->status = (string) $formData['fstatus'];

                    if ($myUser->update()) {
                        // Upload Image Process
                        if (strlen($_FILES['favatar']['name']) > 0) {
                            $this->uploadImage($myUser->name, $myUser->id);
                        }

                        $this->flash->success('User: <strong>'. $myUser->name .'</strong> updated successfully.');
                    } else {
                        $this->flash->error('Have a problem while updating post process. Try again later.');
                    }

                } else {
                    $messageList = '';
                    foreach ($error as $errName => $errMessage) {
                        $messageList .= $errMessage . '<br/>';
                    }
                    $this->flash->error($messageList);
                }
            } else {
                $this->flash->error('Token missmatch. Re enter page from browser toolbar and try again later.');
            }
        }

        $this->tag->appendTitle('Edit: '. (string) $formData['fname'] .'');
        $this->view->setVars([
            'formData' => $formData,
            'myUser' => $myUser,
            'statusList' => \Model\User::getStatusList(),
            'roleList' => \Model\User::getRoleList(),
        ]);
    }

    /**
     * Delete existed user
     */
    public function deleteAction($id, $redirect)
    {
        $redirectUrl = urldecode(base64_decode($redirect));

        $id = (int) $id;
        $myUser = \Model\User::findFirst(['id = :id:', 'bind' => ['id' => (int) $id]])->delete();

        if ($myUser) {
            $this->flashSession->success('User with ID <strong>'. $id .'</strong> was deleted successfully.');
        } else {
            $this->flashSession->error('Have a problem while deleting process with ID <strong>'. $id .'</strong>');
        }

        header('location: '. $redirectUrl .'');
        exit;
    }

    /**
     * Avatar image upload
     */
    public function uploadImage($title, $id)
    {
        $curDateDir = Helper::getCurrentDateDirName(); 
        $extPart = substr(strrchr($_FILES['favatar']['name'],'.'),1);
        $namePart =  Helper::codau2khongdau($title, true) . '-' . $id . time();
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
            $myUser = \Model\User::findFirst([
                'id = :id:',
                'bind' => ['id' => $id]
            ]);
            $myUser->avatar = (string) $filepath;
            $myUser->save();
        }
    }

    /**
     * Check required form field when create a user
     */
    public function addActionValidator($formData, &$error) 
    {
        $pass = true;

        if ($formData['fstatus'] == 0) {
            $error['statusIsRequired'] = $this->lang->_('StatusIsRequired');
            $pass = false;
        }

        if (strlen($formData['fname']) <= 6) {
            $error['nameIsRequired'] = $this->lang->_('NameIsRequired');
            $pass = false;
        }

        if (strlen($formData['fpassword']) == 0) {
            $error['passwordIsRequired'] = $this->lang->_('PasswordIsRequired');
            $pass = false;
        }

        if (strlen($formData['femail']) > 0) {
            $myUser = \Model\User::findFirst(['email = :femail:', 'bind' => ['femail' => (string) $formData['femail']]]);

            if (!empty($myUser)) {
                $error['emailExisted'] = $this->lang->_('EmailExisted');
                $pass = false;
            }
        }

        if (isset($formData['frole']) && $formData['frole'] == 0) {
            $error['roleIsRequired'] = $this->lang->_('RoleIsRequired');
            $pass = false;
        }

        if (strlen($formData['fpassword']) > 0 && strlen($formData['fpassword2']) > 0) {
            if ($formData['fpassword'] !== $formData['fpassword2']) {
                $error['passwordNotMatch'] = $this->lang->_('PasswordNotMatch');
                $pass = false;
            }
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

    /**
     * Check required form field when edit existed user
     */
    public function editActionValidator($formData, &$error) 
    {
        $pass = true;

        if (strlen($formData['fname']) <= 6) {
            $error['nameIsRequired'] = $this->lang->_('NameIsRequired');
            $pass = false;
        }

        if (isset($formData['frole']) && $formData['frole'] == 0) {
            $error['roleIsRequired'] = $this->lang->_('RoleIsRequired');
            $pass = false;
        }

        if ($formData['fstatus'] == 0) {
            $error['statusIsRequired'] = 'Status is required';
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
}