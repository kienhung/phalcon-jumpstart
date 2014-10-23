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

use \Phalcon\Db\Column as Column;
use Jumpstart\Helper;
/**
 * Create application data structure in MySQL database
 */
class InstallController extends BaseController
{
    public function indexAction()
    {
        $formData = $success = $error = [];

        if (!$this->checkinstallrequirement()) {
            die('Install Error. First User Account Already Existed.
              You can remove this install script from Site Controller');
        } else {
            if (isset($_POST['fsubmit'])) {
                $formData = array_merge($formData, $_POST);
                if ($this->installValidator($formData, $error)) {
                    $tableExisted = false;
                    if ($this->usertablesExists()) {
                        $tableExisted = true;
                    } else {
                        try {
                            // Create table USER
                            $stmt1 = $this->db->createTable(
                                'user',
                                null,
                                array(
                                    'columns' => array(
                                        new Column("id",
                                            array(
                                                "primary"       => true,
                                                "type"          => Column::TYPE_INTEGER,
                                                "size"          => 11,
                                                "notNull"       => true,
                                                "autoIncrement" => true,
                                            )
                                        ),
                                        new Column("name",
                                            array(
                                                "type"          => Column::TYPE_VARCHAR,
                                                "size"          => 255,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("email",
                                            array(
                                                "type"          => Column::TYPE_VARCHAR,
                                                "size"          => 50,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("password",
                                            array(
                                                "type"          => Column::TYPE_VARCHAR,
                                                "size"          => 100,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("role",
                                            array(
                                                "type"          => Column::TYPE_INTEGER,
                                                "size"          => 4,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("avatar",
                                            array(
                                                "type"          => Column::TYPE_VARCHAR,
                                                "size"          => 155,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("status",
                                            array(
                                                "type"          => Column::TYPE_INTEGER,
                                                "size"          => 2,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("created_at",
                                            array(
                                                "type"          => Column::TYPE_DATETIME,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("updated_at",
                                            array(
                                                "type"          => Column::TYPE_DATETIME,
                                                "notNull"       => false,
                                            )
                                        )
                                    ),
                                    "options" => array(
                                        "ENGINE"          => "InnoDB",
                                        "TABLE_COLLATION" => "utf8_general_ci",
                                    ),
                                )
                            );

                            // Create table CRONTASK
                            $stmt2 = $this->db->createTable(
                                'crontask',
                                null,
                                array(
                                    'columns' => array(
                                        new Column("id",
                                            array(
                                                "primary"       => true,
                                                "type"          => Column::TYPE_INTEGER,
                                                "size"          => 11,
                                                "notNull"       => true,
                                                "autoIncrement" => true,
                                            )
                                        ),
                                        new Column("task",
                                            array(
                                                "type"          => Column::TYPE_VARCHAR,
                                                "size"          => 50,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("action",
                                            array(
                                                "type"          => Column::TYPE_VARCHAR,
                                                "size"          => 50,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("ipaddress",
                                            array(
                                                "type"          => Column::TYPE_VARCHAR,
                                                "size"          => 50,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("timeprocessing",
                                            array(
                                                "type"          => Column::TYPE_FLOAT,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("output",
                                            array(
                                                "type"          => Column::TYPE_TEXT,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("status",
                                            array(
                                                "type"          => Column::TYPE_INTEGER,
                                                "size"          => 2,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("created_at",
                                            array(
                                                "type"          => Column::TYPE_DATETIME,
                                                "notNull"       => false,
                                            )
                                        )
                                    ),
                                    "options" => array(
                                        "ENGINE"          => "MyISAM",
                                        "TABLE_COLLATION" => "utf8_general_ci",
                                    ),
                                )
                            );

                            // Create table LOGS
                            $stmt3 = $this->db->createTable(
                                'logs',
                                null,
                                array(
                                    'columns' => array(
                                        new Column("id",
                                            array(
                                                "primary"       => true,
                                                "type"          => Column::TYPE_INTEGER,
                                                "size"          => 11,
                                                "notNull"       => true,
                                                "autoIncrement" => true,
                                            )
                                        ),
                                        new Column("name",
                                            array(
                                                "type"          => Column::TYPE_VARCHAR,
                                                "size"          => 32,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("type",
                                            array(
                                                "type"          => Column::TYPE_INTEGER,
                                                "size"          => 3,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("content",
                                            array(
                                                "type"          => Column::TYPE_TEXT,
                                                "notNull"       => false,
                                            )
                                        ),
                                        new Column("created_at",
                                            array(
                                                "type"          => Column::TYPE_INTEGER,
                                                "size"          => 10,
                                                "notNull"       => false,
                                            )
                                        ),
                                    ),
                                    "options" => array(
                                        "ENGINE"          => "MyISAM",
                                        "TABLE_COLLATION" => "utf8_general_ci",
                                    ),
                                )
                            );

                            if ($stmt1 == true && $stmt2 == true && $stmt3 == true) {
                                //table create ok
                                $tableExisted = true;
                            }
                        } catch (Exception $e) {
                            $this->flash->error('Error while creating main user tables. <br />(Error Code: ' .
                                $e->getCode() . ', <br />Error Message: ' . $e->getMessage() . ')');
                        }
                    }

                    if ($tableExisted) {
                        //begin create new account
                        $myUser = new \Model\User();
                        $myUser->name = (string) \Other\Encoding::toUTF8($formData['fname']);
                        $myUser->email = (string) $formData['femail'];
                        $myUser->password = (string) $this->security->hash($formData['fpassword']);
                        $myUser->role = ROLE_ADMIN;
                        $myUser->status = \Model\User::STATUS_ENABLE;

                        if ($myUser->create()) {
                            $this->flashSession->success('Administrator Account had been created.');
                            return $this->response->redirect('admin/login');
                        } else {
                            $this->flash->error('Error while creating Administrator Account. Please try again.');
                        }

                    }
                } else {
                    $messageList = '';
                    foreach ($error as $errName => $errMessage) {
                        $messageList .= $errMessage . '<br/>';
                    }
                    $this->flash->error($messageList);
                }
            }
        }

        $this->tag->appendTitle('Install');
        $this->view->setVars([
            'formData'      => $formData,
            'redirectUrl'   => base64_encode(urlencode(Helper::getCurrentUrl()))
        ]);
    }

    /**
     * Check required field
     * 
     * @return boolean
     */
    private function installValidator($formData, &$error)
    {
        $pass = true;

        if (strlen($formData['fname']) == 0) {
            $pass = false;
            $error[] = 'Administrator Name is required.';
        }

        if (!Helper::ValidatedEmail($formData['femail'])) {
            $pass = false;
            $error[] = 'Administrator Email is not valid.';
        }

        if (strlen($formData['fpassword']) == 0) {
            $pass = false;
            $error[] = 'Administrator Password is required.';
        }

        if (strcmp($formData['fpassword'], $formData['fpassword2']) != 0) {
            $pass = false;
            $error[] = 'Password and confirm password is not match.';
        }

        return $pass;
    }

    /**
     * Check if User table existed
     * 
     * @return boolean
     */
    private function usertablesExists()
    {
        $tables = array();
        $tableList = $this->db->listTables();
        foreach ($tableList as $tableName) {
            $tables[] = $tableName;
        }
        
        return in_array('user', $tables);

    }

    /**
     * Check if there is no user in system before run install
     */
    private function checkinstallrequirement()
    {
        $needInstall = false;

        //Check User tables exists
        if (!$this->usertablesExists()) {
            $needInstall = true;
        } else {
            $userCount = \Model\User::count();

            if ($userCount > 0) {
                $needInstall = false;
            } else {
                $needInstall = true;
            }
        }

        return $needInstall;
    }
}
