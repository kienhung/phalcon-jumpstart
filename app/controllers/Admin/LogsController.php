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

class LogsController extends BaseController
{
    protected $recordPerPage = 30;

    public function indexAction()
    {
        $formData = [];
        $paginateUrl = $this->getControllerUrl();
        $page = $this->request->getQuery('page', 'int', 1);

        if (!empty($_POST['fsubmitbulk'])) {
            if (!isset($_POST['fbulkid'])) {
                $this->flash->warning($this->lang->_('ErrorNoBulkSelected'));
            } else {
                if ($this->security->checkToken()) {
                    $formData['fbulkid'] = $_POST['fbulkid'];

                    if ($_POST['fbulkaction'] == 'delete') {
                        $deletearr = $_POST['fbulkid'];

                        // Start a transaction
                        $this->db->begin();
                        $successId = [];
                        $errorId = [];

                        foreach ($deletearr as $deleteid) {
                            $myLogs = \Model\Logs::findFirst(['id = :id:', 'bind' => ['id' => (int) $deleteid]])->delete();

                            // If fail stop a transaction
                            if ($myLogs == false) {
                                $this->db->rollback();
                                $errorId[] = $deleteid;
                                return;
                            } else {
                                $successId[] = $deleteid;
                            }
                        }
                        // Commit a transaction
                        if ($this->db->commit() == true) {
                            $this->flash->success(str_replace('##ID##', ': :' . implode(', ', $successId) . '"', $this->lang->_('SuccessDeleteData')));

                            $formData['fbulkid'] = null;
                        } else {
                            $this->flash->error(str_replace('##ID##', implode(', ', $errorId), $this->lang->_('ErrorDeleteData')));
                        }
                    } else {
                        $this->flash->warning($this->lang->_('ErrorNoActionSelected'));
                    }
                } else {
                    $this->flash->error($this->lang->_('ErrorTokenMissMatch'));
                }
            }
        }

        // SortBy
        if (isset($_GET['sortby'])) {
            $formData['sortby'] = (string) $_GET['sortby'];
        } else {
            $formData['sortby'] = (string) 'id';
        }
        $paginateUrl .= '?sortby=' . $formData['sortby'];

        // SortType
        if (isset($_GET['sorttype'])) {
            $formData['sorttype'] = (string) $_GET['sorttype'];
        } else {
            $formData['sorttype'] = (string) 'DESC';
        }
        $paginateUrl .= '&sorttype=' . $formData['sorttype'];

        // Search
        if (isset($_GET['keyword'])) {
            $formData['keyword'] = (string) $_GET['keyword'];
            $paginateUrl .= '&keyword=' . $formData['keyword'];
        }

        $myBuilder = \Model\Logs::getLogss($formData);

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder([
            'builder' => $myBuilder,
            'limit' => $this->recordPerPage,
            'page' => $page
        ]);

        $this->tag->appendTitle('Logs Listing');
        $this->view->page = $paginator->getPaginate();
        $this->view->setVars([
            'formData'      => $formData,
            'redirectUrl'   => base64_encode(urlencode(Helper::getCurrentUrl())),
            'paginateUrl'   => $paginateUrl,
        ]);
    }

    public function addAction()
    {
        $formData = $error = [];

        if (!empty($_POST['fsubmit'])) {
            if ($this->security->checkToken()) {
                $formData = array_merge($formData, $_POST);

                if ($this->addActionValidator($formData, $error)) {
                    $myLogs = new \Model\Logs();
                    
                    $myLogs->name = (string) $formData['name'];
                    $myLogs->type = (int) $formData['type'];
                    $myLogs->content = (string) $formData['content'];


                    if ($myLogs->create()) {
                        
                        $this->flash->success($this->lang->_('SuccessInsertData'));
                    } else {
                        $this->flash->error($this->lang->_('ErrorInsertData'));
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

        $this->tag->appendTitle('Add new logs');
        $this->view->setVars([
            'formData' => $formData,
			'typeList' => \Model\Logs::getTypeList(),
        ]);
    }

    public function editAction($id)
    {
        $formData = $error = [];
        $id = (int) $id;

        $myLogs = \Model\Logs::findFirst(['id = :id:', 'bind' => ['id' => (int) $id]]);
        
        $formData['name'] = (string) $myLogs->name;
        $formData['type'] = (int) $myLogs->type;
        $formData['content'] = (string) $myLogs->content;


        if (!empty($_POST['fsubmit'])) {
            if ($this->security->checkToken()) {
                $formData = array_merge($formData, $_POST);

                if ($this->editActionValidator($formData, $error)) {
                    
                    $myLogs->name = (string) $formData['name'];
                    $myLogs->type = (int) $formData['type'];
                    $myLogs->content = (string) $formData['content'];


                    if ($myLogs->update()) {
                        
                        $this->flash->success($this->lang->_('SuccessUpdateData'));
                    } else {
                        $this->flash->error($this->lang->_('ErrorInsertData'));
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

        $this->tag->appendTitle('Edit: Edit a logs');
        $this->view->setVars([
            'formData' => $formData,
			'typeList' => \Model\Logs::getTypeList(),
        ]);
    }

    public function deleteAction($id, $redirect)
    {
        $redirectUrl = urldecode(base64_decode($redirect));

        $id = (int) $id;
        $myLogs = \Model\Logs::findFirst(['id = :id:', 'bind' => ['id' => (int) $id]])->delete();

        if ($myLogs) {
            $this->flashSession->success(str_replace('##ID##', $id, $this->lang->_('SuccessDeleteData')));
        } else {
            $this->flashSession->error(str_replace('##ID##', $id, $this->lang->_('ErrorDeleteData')));
        }

        header('location: '. $redirectUrl .'');
        exit;
    }

    public function addActionValidator($formData, &$error) {
        $pass = true;
        

        return $pass;
    }

    public function editActionValidator($formData, &$error) {
        $pass = true;
        

        return $pass;
    }
    
}