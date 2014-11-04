<?php

namespace Controller\{{CONTROLLER_NAMESPACE}};

use Jumpstart\Helper;
use Other\Uploader;
use Other\ImageResizer;

class {{CONTROLLER_CLASS}} extends BaseController
{
    protected $recordPerPage = {{CONTROLLER_RECORDPERPAGE}};

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
                            $my{{MODULE}} = \{{MODULE_NAMESPACE}}\{{MODULE}}::findFirst(['id = :id:', 'bind' => ['id' => (int) $deleteid]])->delete();

                            // If fail stop a transaction
                            if ($my{{MODULE}} == false) {
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
            $formData['sortby'] = (string) '{{primarykey}}';
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

        $myBuilder = \{{MODULE_NAMESPACE}}\{{MODULE}}::get{{MODULE}}s($formData);

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder([
            'builder' => $myBuilder,
            'limit' => $this->recordPerPage,
            'page' => $page
        ]);

        $this->tag->appendTitle('{{MODULE}} Listing');
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
                    $my{{MODULE}} = new \{{MODULE_NAMESPACE}}\{{MODULE}}();
                    {{PROPERTY_ADD_LIST}}

                    if ($my{{MODULE}}->create()) {
                        {{IMAGE_UPLOAD_ADD}}
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

        $this->tag->appendTitle('Add new {{MODULE_LOWER}}');
        $this->view->setVars([
            'formData' => $formData,{{PARAMETER_TEMPLATE_LIST}}
        ]);
    }

    public function editAction($id)
    {
        $formData = $error = [];
        $id = (int) $id;

        $my{{MODULE}} = \{{MODULE_NAMESPACE}}\{{MODULE}}::findFirst(['id = :id:', 'bind' => ['id' => (int) $id]]);
        {{FORMDATA_EDIT_LIST}}

        if (!empty($_POST['fsubmit'])) {
            if ($this->security->checkToken()) {
                $formData = array_merge($formData, $_POST);

                if ($this->editActionValidator($formData, $error)) {
                    {{PROPERTY_EDIT_LIST}}

                    if ($my{{MODULE}}->update()) {
                        {{IMAGE_UPLOAD_EDIT}}
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

        $this->tag->appendTitle('Edit: Edit a {{MODULE_LOWER}}');
        $this->view->setVars([
            'formData' => $formData,{{PARAMETER_TEMPLATE_LIST}}
        ]);
    }

    public function deleteAction($id, $redirect)
    {
        $redirectUrl = urldecode(base64_decode($redirect));

        $id = (int) $id;
        $my{{MODULE}} = \{{MODULE_NAMESPACE}}\{{MODULE}}::findFirst(['id = :id:', 'bind' => ['id' => (int) $id]])->delete();

        if ($my{{MODULE}}) {
            $this->flashSession->success(str_replace('##ID##', $id, $this->lang->_('SuccessDeleteData')));
        } else {
            $this->flashSession->error(str_replace('##ID##', $id, $this->lang->_('ErrorDeleteData')));
        }

        header('location: '. $redirectUrl .'');
        exit;
    }

    public function addActionValidator($formData, &$error) {
        $pass = true;
        {{VALIDATEDADD}}
        return $pass;
    }

    public function editActionValidator($formData, &$error) {
        $pass = true;
        {{VALIDATEDEDIT}}
        return $pass;
    }
    {{IMAGE_UPLOAD_FUNCTION}}
}