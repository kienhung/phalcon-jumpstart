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

class GeneratorController extends BaseController
{
    public function indexAction()
    {
        $this->tag->appendTitle('Table Listing');
        $this->view->setVars([
            'listTables' => $this->db->listTables(),
        ]);
    }

    public function gentableAction($tableName)
    {   
        $formData = $error = $success = [];
        $formData['tblName'] = (string) $tableName;
        $formData['modelName'] = (string) preg_replace('/_/', '', ucfirst($tableName));
        $formData['controllerClass'] = (string) $formData['modelName'];
        $indexColumnData = array();
        $formData['primarykey'] = '';
        // $refl = new \ReflectionClass('\Phalcon\Db\Column');
        // $constList = $refl->getConstants();
        // var_dump($constList);
        $fields = $this->db->describeColumns($formData['tblName']);

        foreach ($fields as $field) {
            $label = preg_replace('/_/', ' ', $field->getName());
            $typeName = substr(\Model\Generator::getTypeName($field->getType()), 5);
            $inputType = $typeName == 'TEXT' ? 'textarea' : 'text';
            $formData['columns'][] = [
                'name'      =>  $field->getName(),
                'type'      =>  $field->getType(),
                'typeName'  =>  $typeName,
                'size'      =>  $field->getSize(),
                'isNumeric' =>  $field->isNumeric(),
                'isPrimary' =>  $field->isPrimary(),
                'isNotNull' =>  $field->isNotNull(),
                'label'     =>  ucfirst($label),
                'inputType' =>  $inputType

            ];
            if ($field->isPrimary()) {
                $formData['primarykey'] = $field->getName();
            }
        }
        // Referenced list
        $formData['localReferCol'] = array();
        $myReferencedList = $this->db->describeReferences($formData['tblName'], $this->config->database->dbname);
        if ($myReferencedList == true) {
            foreach ($myReferencedList as $referenceColumn) {
                foreach ($referenceColumn->getColumns() as $refColName) {
                    $formData['localReferCol'][] = $refColName;   
                }
            }
        }
        
        // Indexes List
        $formData['indexesCol'] = array();
        $myIndexList = $this->db->describeIndexes($formData['tblName'], $this->config->database->dbname);
        if ($myIndexList == true) {
            foreach ($myIndexList as $indexes) {
                foreach ($indexes->getColumns() as $indexCol) {
                    $formData['indexesCol'][] = $indexCol;
                }
            }          
        }
        
        if (!empty($_POST['fsubmit'])) {
            if ($this->security->checkToken()) {
                $formData = array_merge($formData, $_POST);

                $this->genM($formData, $error, $success);
                $this->genC($formData, $error, $success);
                $this->genT($formData, $error, $success);
                $this->genL($formData, $error, $success);

                if (count($success) > 0) {
                    $messageList = '';
                    foreach ($success as $mes) {
                        $messageList .= $mes .'<br />';
                    }
                    $this->flash->success($messageList);
                }
            } else {
                $this->flash->error('Token missmatch. Re enter page from browser toolbar and try again later.');
            }
        }

        $validateType = [
            'notneed' => 'Not Need',
            'notempty' => 'Not Empty String',
            'greaterthanzero' => 'Number greater than zero (0)',
            'email' => 'Email Address',
            'uploadrequied' => 'Upload Required'
        ];
        $formData['validateType'] = $validateType;

        // INPUT TYPE
        $inputType = [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'editor' => 'Editor',
            //'select' => 'Select',
            'imageupload' => 'Image Upload'
        ];
        $formData['inputType'] = $inputType;

        // LOAD MODEL IN FOLDER MODELS
        $excludeModel = [
            'BaseModel',
            'Generator'
        ];
        $includeModel = array();
        $columnModel = array();
        $modelDir =  $this->config->application->modelsDir;
        if (is_dir($modelDir)) {
            $modelFiles = glob($modelDir . '*.php');
            if (count($modelFiles) > 0) {
                $i = 0;
                foreach ($modelFiles as $file) {
                    $modelName = preg_replace('/\.php$/', '', $file);
                    $modelName = explode('/', $modelName);
                    $modelName = $modelName[count($modelName) - 1];

                    $className = '\\Model\\' . $modelName;
                    $objectTable = new $className();
                    $objectTableName = $objectTable->getSource();

                    if (!in_array($modelName, $excludeModel) && $this->db->tableExists($objectTableName)) {
                        if ($i == 0) {
                            $columnModel = $this->getColumnByModel($modelName);
                        }
                        $includeModel[] = $modelName;
                        $i++;
                    }
                }
            }
        }
        $formData['includeModel'] = $includeModel;
        $formData['columnModel'] = $columnModel;

        // var_dump($formData);
        $this->tag->appendTitle('Table Generator');

        $this->view->setVars([
            'formData'      =>  $formData
        ]);  
    }

    private function genM($formData, &$error, &$success)
    {
        $valid = true;
        $content = "<?php \n";
        $dir = $this->config->application->modelsDir;

        $s['{{MODULE}}'] = ucfirst($formData['fmodel']);
        $s['{{MODULE_NAMESPACE}}'] = (string) $formData['fmodelnamespace'];

        if (is_dir($dir) && is_writable($dir)) {
            $file = fopen($dir . $formData['fmodel'] . '.php', 'w');

            if ($file) {
                $content .= "namespace " . $formData['fmodelnamespace'] . "; \n\n";
                // OPEN CLASS
                $content .= "class " . $formData['fmodel'] . " extends " . $formData['fmodelbaseclass'] . " \n";
                $content .= "{ \n\t";

                if (!empty($formData['fconstantable'])) {
                    foreach ($formData['fconstantable'] as $constable) {
                        if (strlen($constable) > 0) {
                            $constArray = explode(',', $constable);

                            // gen const value at top of a file
                            for ($i = 0; $i < count($constArray); $i++) {
                                $strArray = explode(':', $constArray[$i]);

                                $constKey = $strArray[0];
                                $constValue = $strArray[1];
                                $constName = $strArray[2];
                                
                                $content .= "const ". $constKey ." = ". $constValue ."; \n\t";
                            }
                        }
                    }
                }
                $content .= "\n\t";

                // gen getsource function
                $content .= 'public function getSource()' . "\n\t{\n\t\t";
                $content .= 'return \''. $formData['tblName'] .'\';' . "\n\t";
                $content .= '}' . "\n\n\t";

                foreach ($formData['columns'] as $column) {
                    $colArray[] = $column['name']; 
                }

                // gen beforecreate function
                $content .= "public function beforeCreate()\n\t{\n\t\t";
                if (in_array('created_at', $colArray)) {
                    $content .= '$this->created_at = date(\'Y-m-d H:i:s\');' . "\n\t";
                }
                $content .= "\n\t}\n\n\t"; 

                // gen beforeupdate function
                $content .= "public function beforeUpdate()\n\t{\n\t\t";
                if (in_array('updated_at', $colArray)) {
                    $content .= '$this->updated_at = date(\'Y-m-d H:i:s\');' . "\n\t";
                }
                $content .= "\n\t}\n\n\t";

                //initialize function
                $content .= "public function initialize()\n\t{\n\t\t";

                if (!empty($formData['frelationtype'])) {
                    foreach ($formData['frelationtype'] as $localRefField => $relType) {
                        if (strlen($relType) > 0) {
                            // Referenced list
                            $myReferencedList = $this->db->describeReferences($formData['tblName'], $this->config->database->dbname);
                            if ($myReferencedList == true) {
                                foreach ($myReferencedList as $foreign) {
                                    $localColumn = $foreign->getColumns();
                                    if ($localRefField == $localColumn[0]) {
                                        $refTable = $foreign->getReferencedTable();

                                        $refField = $foreign->getReferencedColumns();

                                        $refContent = '$this->' . self::getRelationString($relType) . '(\''. $localRefField .'\', ';
                                        $refContent .= '\''. '#\#' . $formData['fmodelnamespace'] .'#\#'. ucfirst($refTable) .'\', ' ;
                                        $refContent .= '\''. $refField[0] .'\', [\'alias\' => \''. $refTable .'\']);' . "\n\t\t";
                                        $refContent = str_replace('#', '', $refContent); 
                                        $content .= $refContent;
                                    }
                                }
                            }       
                        }
                    }
                }
                
                $content .= "\n\t}\n\n\t";

                // ========================================================================
                // gen get Object List
                // ========================================================================
                $content .= "public static function get" . $s['{{MODULE}}'] . "s(\$formData)\n\t{";

                // init params
                $content .= "\n\t\t\$conditions = '';";
                $content .= "\n\t\t\$bind = [];";

                // DESC & ASC
                $content .= "\n\n\t\tif (!isset(\$formData['sorttype'])) {";
                $content .= "\n\t\t\t\$sorttype = (string) 'DESC';";
                $content .= "\n\t\t} else {";
                $content .= "\n\t\t\t\$sorttype = (string) \$formData['sorttype'];";
                $content .= "\n\t\t}";

                // ORDER BY
                $content .= "\n\n\t\tif (!isset(\$formData['sortby'])) {";
                $content .= "\n\t\t\t\$sortby = (string) '". $formData['primarykey'] ."';";
                if (isset($formData['fsortable'])) {
                    foreach ($formData['fsortable'] as $key => $sortable) {
                        $inputName = preg_replace('/_/', '', $key);
                        $inputName = strtolower($inputName);

                        $content .= "\n\t\t} elseif (\$formData['sortby'] == '{$inputName}') {";
                        $content .= "\n\t\t\t\$sortby = (string) '{$key}';";
                    }
                }
                $content .= "\n\t\t} else {";
                $content .= "\n\t\t\t\$sortby = (string) \$formData['sortby'];";
                $content .= "\n\t\t}";

                // LIKE
                $searchKeyword = '';
                if (isset($formData['fsearchabletext'])) {
                    $counter = count($formData['fsearchabletext']);
                    if ($counter > 0) {
                        if ($counter == 1) {
                            $keys = array_keys($formData['fsearchabletext']);
                            $key = $keys[0];
                            $searchKeyword .= $key . ' LIKE "%\' . $formData[\'keyword\'] . \'%"';
                        } else {
                            foreach ($formData['fsearchabletext'] as $key => $value) {
                                $searchKeyword .= ($searchKeyword != '' ? ' OR ' : '') . '(' . $key . ' LIKE "%\' . $formData[\'keyword\'] . \'%")';
                            }
                        }
                    }
                }
                if (!empty($searchKeyword)) {
                    $content .= "\n\n\t\tif (isset(\$formData['keyword'])) {";
                    $content .= "\n\t\t\t\$conditions = (string) '{$searchKeyword}';";
                    $content .= "\n\t\t} else {";
                    $content .= "\n\t\t\t\$conditions = '';";
                    $content .= "\n\t\t}";
                }

                // PREPAIR PARAMETERS
                $content .= "\n\n\t\t\$parameters = [";
                $content .= "\n\t\t\t'models' => ['Model\\" . $s['{{MODULE}}'] . "'],";
                $content .= "\n\t\t\t'column' => ['*'],";
                $content .= "\n\t\t\t'conditions' => \$conditions,";
                $content .= "\n\t\t\t'order' => ['Model\\" . $s['{{MODULE}}'] . ".' . \$sortby . ' ' . \$sorttype . ''],";
                $content .= "\n\t\t];";

                // RETURN DATA
                $content .= "\n\n\t\t\$builder = new \Phalcon\Mvc\Model\Query\Builder(\$parameters);";
                $content .= "\n\t\treturn \$builder;";

                $content .= "\n\t}\n\n\t";

                // ========================================================================
                // gen get CONST List & CONST Name
                // ========================================================================
                foreach ($formData['fconstantable'] as $key => $value) {
                    if ($value != '') {
                        $constantArr = explode(',' , $value);
                        $constant = array();
                        foreach ($constantArr as $con) {
                            $conArr = explode(':', $con);
                            if (count($conArr) > 0) {
                                $constant[] = array(
                                    'name' => $conArr[0],
                                    'value' => $conArr[1],
                                    'text' => $conArr[2]
                                );
                            }
                        }
                        $content .= "public function get" . ucfirst($key) . "Name()\n\t{\n\t\t";
                        $content .= '$name = \'\';' . "\n\n\t\t";
                        $content .= 'switch ($this->' . $key . ') {'. "\n\t\t\t" .'';
                        foreach ($constant as $const) {
                            $content .= 'case self::'. $const['name'] .':' . "\n\t\t\t\t";
                            $content .= '$name = \''. $const['text'] .'\';' . "\n\t\t\t\t";
                            $content .= 'break;' . "\n\t\t\t";
                        }
                        $content .= "\n\t\t}\n\n\t\t";
                        $content .= 'return $name;';
                        $content .= "\n\t}\n\n\t";

                        $content .= "public static function get" . ucfirst($key) . "List()\n\t{\n\t\t";
                        $content .= '$output = array();' . "\n\n\t\t";
                        foreach ($constant as $const) {
                            $content .= '$output[self::'. $const['name'] .'] = \''. $const['text'] .'\';' . "\n\t\t";
                        }
                        $content .= "\n\t\t" . 'return $output;' . "\n\t}\n\n";
                    }
                }

                // FUNCTION GET OBJECT LIST
               /* $i = 1;
                foreach ($formData['finputtype'] as $key => $inputType) {
                    if ($inputType == 'select') {
                        if ($i == 1) {
                            $content .= "\t";
                        }
                        $modelName = $formData['fselectmodel'][$key];
                        $selectValue = $formData['fselectvalue'][$key];
                        $selectText = $formData['fselecttext'][$key];
                        $condition = $formData['fselectcondition'][$key];

                        $content .= "public static function get" . ucfirst(strtolower($modelName)) . "List()\n\t{\n\t\t";
                        $content .= '$data = array();' . "\n\t\t";
                        if (!empty($condition)) {
                            $content .= '$object = \\' . $s['{{MODULE_NAMESPACE}}'] . '\\' . $modelName . '::find("' . $condition . '");'. "\n\t\t";
                        } else {
                            $content .= '$object = \\' . $s['{{MODULE_NAMESPACE}}'] . '\\' . $modelName . '::find();'. "\n\t\t";
                        }
                        $content .= 'if (count($object) > 0) {' . "\n\t\t\t";
                        $content .= 'foreach($object as $ob) {' . "\n\t\t\t\t";
                        $content .= '$data[$ob->' . $selectValue . '] = $ob->' . $selectText . ";\n\t\t\t";
                        $content .= '}' . "\n\t\t";
                        $content .= '}';
                        $content .= "\n\t\t" . 'return $data;' . "\n\t}\n\n\t";
                        $i++;
                    }
                }*/

                // END CLASS
                $content .= "\n}\n";

                $result = fwrite($file, $content);

                /*if (!$result) {
                    echo 'cannot write';
                } else {
                    echo 'write ok';
                }*/
            } else {
                $error[] = 'File cannot Write to Models Directory. CHMOD 755 for this directory and try again';
            }
        }
        return $valid;
    }

    protected function getRelationString($relationType)
    {
        $refString = '';

        switch ($relationType) {
            case '1-1':
                $refString = (string) 'hasOne';
                break;
            case '1-n':
                $refString = (string) 'hasMany';
                break;
            case 'n-1':
                $refString = (string) 'belongsTo';
                break;
            case 'n-n':
                $refString = (string) 'hasManyToMany';
                break;
        }

        return $refString;
    }

    private function genC($formData, &$error, &$success)
    {
        $valid = true;
        $dir = $this->config->application->controllersDir . 'admin/';
        $s = array();

        //template files
        $source_basedir = $this->config['application']['viewsDir'] . 'admin/generator/generate_format/';
        $source['model'] = $source_basedir . 'model.volt';
        $source['controlleradmin'] = $source_basedir . 'controller_admin.volt';
        $source['languageadmin'] = $source_basedir . 'language_admin.volt';
        $source['controlleradminindex'] = $source_basedir . 'controller_admin_index.volt';
        $source['controlleradminadd'] = $source_basedir . 'controller_admin_add.volt';
        $source['controlleradminedit'] = $source_basedir . 'controller_admin_edit.volt';

        if ($formData['fcontrollerrecordperpage'] <= 0) {
            $formData['fcontrollerrecordperpage'] = 30;
        }

        $s['{{MODULE_NAMESPACE}}'] = (string) $formData['fmodelnamespace'];
        $s['{{MODULE}}'] = ucfirst($formData['fmodel']);
        $s['{{MODULE_LOWER}}'] = strtolower($formData['fmodel']);
        $s['{{CONTROLLER_RECORDPERPAGE}}'] = (int) $formData['fcontrollerrecordperpage'];
        $s['{{CONTROLLER_ICONCLASS}}'] = $formData['fcontrollericonclass'];
        $s['{{CONTROLLER_CLASS}}'] = $formData['fcontrollerclass'];
        $s['{{CONTROLLER_NAMESPACE}}'] = $formData['fcontrollernamespace'];
        $s['{{primarykey}}'] = $formData['primarykey'];
        // =================================================================
        // Replacement search for index action
        // =================================================================
        $searchKeyword = '';
        if (isset($formData['fsearchabletext'])) {
            $counter = count($formData['fsearchabletext']);
            if ($counter > 0) {
                if ($counter == 1) {
                    $keys = array_keys($formData['fsearchabletext']);
                    $key = $keys[0];
                    $searchKeyword .= $key . ' LIKE :search:';
                } else {
                    foreach ($formData['fsearchabletext'] as $key => $value) {
                        $searchKeyword .= ($searchKeyword != '' ? ' OR ' : '') . '(' . $key . ' LIKE :search:)';
                    }
                }
            }
        }
        $s['{{SEARCH_KEYWORD}}'] = $searchKeyword;
        $s['{{SEARCH_IN}}'] = 'search';

        // =================================================================
        // Replacement validate for add & edit action
        // =================================================================
        $validateStringAdd = $validateStringEdit = "\n";
        foreach ($formData['fvalidating'] as $key => $validate) {
            $columnName = preg_replace('/_/', '', $key);
            $columnName = ucfirst($columnName);
            $inputName = strtolower($columnName);
            $inputType = $formData['finputtype'][$key];
            switch ($validate) {
                case 'notempty':
                    $validateStringAdd .= "        if (\$formData['{$inputName}'] == '') {\n";
                    $validateStringAdd .= "            \$error['{$key}IsRequired'] = \$this->lang->_('{$columnName}IsRequired');\n";
                    $validateStringAdd .= "            \$pass=false;\n";
                    $validateStringAdd .= "        }\n\n";

                    $validateStringEdit .= "        if (\$formData['{$inputName}'] == '') {\n";
                    $validateStringEdit .= "            \$error['{$key}IsRequired'] = \$this->lang->_('{$columnName}IsRequired');\n";
                    $validateStringEdit .= "            \$pass=false;\n";
                    $validateStringEdit .= "        }\n\n";
                    break;
                case 'greaterthanzero':
                    $validateStringAdd .= "        if (\$formData['{$inputName}'] == 0) {\n";
                    $validateStringAdd .= "            \$error['{$key}IsGreaterThanZero'] = \$this->lang->_('{$columnName}GreaterThanZero');\n";
                    $validateStringAdd .= "            \$pass=false;\n";
                    $validateStringAdd .= "        }\n\n";

                    $validateStringEdit .= "        if (\$formData['{$inputName}'] == 0) {\n";
                    $validateStringEdit .= "            \$error['{$key}IsGreaterThanZero'] = \$this->lang->_('{$columnName}GreaterThanZero');\n";
                    $validateStringEdit .= "            \$pass=false;\n";
                    $validateStringEdit .= "        }\n\n";
                    break;
                case 'email':
                    $validateStringAdd .= "        if (!Helper::ValidatedEmail(\$formData['{$inputName}'])) {\n";
                    $validateStringAdd .= "            \$error['{$key}IsNotEmail'] = \$this->lang->_('{$columnName}IsEmail');\n";
                    $validateStringAdd .= "            \$pass=false;\n";
                    $validateStringAdd .= "        }\n\n";

                    $validateStringEdit .= "        if (!Helper::ValidatedEmail(\$formData['{$inputName}'])) {\n";
                    $validateStringEdit .= "            \$error['{$key}IsNotEmail'] = \$this->lang->_('{$columnName}IsEmail');\n";
                    $validateStringEdit .= "            \$pass=false;\n";
                    $validateStringEdit .= "        }\n\n";
                    break;
            }

            if ($inputType == 'imageupload') {
                if ($validate == 'uploadrequied') {
                    $validateStringAdd .= "        if(empty(\$_FILES['{$inputName}']['name'])) {\n";
                    $validateStringAdd .= "            \$error['{$inputName}IsRequired'] = \$this->lang->_('{$inputName}IsRequired');\n";
                    $validateStringAdd .= "            \$pass = false;\n";
                    $validateStringAdd .= "        } else {\n";
                } else {
                    $validateStringAdd .= "        if(strlen(\$_FILES['{$inputName}']['name']) > 0) {\n";
                }
                $validateStringEdit .= "        if(strlen(\$_FILES['{$inputName}']['name']) > 0) {\n";

                $validateStringAdd .= "            //check extension\n";
                $validateStringAdd .= "            \$ext = strtoupper(Helper::fileExtension(\$_FILES['{$inputName}']['name']));\n\n";
                $validateStringAdd .= "            if (!empty(\$ext) && !in_array(\$ext, \$this->setting->" . $s['{{MODULE_LOWER}}'] . "->validExtension->toArray())) {\n";
                $validateStringAdd .= "                \$error['FileTypeNotValid'] = \$this->lang->_('FileTypeNotValid');\n";
                $validateStringAdd .= "                \$pass=false;\n";
                $validateStringAdd .= "            }\n\n";
                $validateStringAdd .= "            if (!empty(\$ext) && \$_FILES['{$inputName}']['size'] > \$this->setting->" . $s['{{MODULE_LOWER}}'] . "->validMaxFileSize) {\n";
                $validateStringAdd .= "                \$error['FileSizeNotValid'] = \$this->lang->_('FileSizeNotValid');\n";
                $validateStringAdd .= "                \$pass=false;\n";
                $validateStringAdd .= "            }\n";

                $validateStringEdit .= "            //check extension\n";
                $validateStringEdit .= "            \$ext = strtoupper(Helper::fileExtension(\$_FILES['{$inputName}']['name']));\n\n";
                $validateStringEdit .= "            if (!empty(\$ext) && !in_array(\$ext, \$this->setting->" . $s['{{MODULE_LOWER}}'] . "->validExtension->toArray())) {\n";
                $validateStringEdit .= "                \$error['FileTypeNotValid'] = \$this->lang->_('FileTypeNotValid');\n";
                $validateStringEdit .= "                \$pass=false;\n";
                $validateStringEdit .= "            }\n\n";
                $validateStringEdit .= "            if (!empty(\$ext) && \$_FILES['{$inputName}']['size'] > \$this->setting->" . $s['{{MODULE_LOWER}}'] . "->validMaxFileSize) {\n";
                $validateStringEdit .= "                \$error['FileSizeNotValid'] = \$this->lang->_('FileSizeNotValid');\n";
                $validateStringEdit .= "                \$pass=false;\n";
                $validateStringEdit .= "            }\n";

                $validateStringAdd .= "        }\n\n";
                $validateStringEdit .= "        }\n\n";
            }
        }
        $s['{{VALIDATEDADD}}'] = $validateStringAdd;
        $s['{{VALIDATEDEDIT}}'] = $validateStringEdit;

        $propertyAddList = "\n";
        $propertyEditList = "\n";
        $formDataEdit = "\n";
        $parameterTemplate = '';
        $imageUploadFunction = '';
        $imageUploadAdd = $imageUploadEdit = '';
        $object = '$my' . $s['{{MODULE}}'];

        foreach ($formData['columns'] as $key => $column) {
            $inputName = preg_replace('/_/', '', $column['name']);
            $inputName = strtolower($inputName);
            $inputType = $formData['finputtype'][$column['name']];

            if ($column['name'] == 'u_id') {
                $propertyAddList .= '                    ' . $object . '->' . $column['name'] . ' = (int) $this->session->get(\'me\')->id;' . PHP_EOL;
            } else if ($column['name'] == 'created_at' || $column['name'] == 'updated_at' || $column['name'] == 'id' || $inputType == 'imageupload') {
                // Do nothing
            } else {
                if ($column['typeName'] == 'VARCHAR' || $column['typeName'] == 'TEXT') {
                    $propertyAddList .= '                    ' .$object . '->' . $column['name'] . ' = (string) $formData[\'' . $inputName . '\'];' . PHP_EOL;
                    $propertyEditList .= '                    ' .$object . '->' . $column['name'] . ' = (string) $formData[\'' . $inputName . '\'];' . PHP_EOL;
                    $formDataEdit .= '        $formData[\'' . $inputName . '\'] = (string) ' . $object . '->' . $column['name'] . ';' . PHP_EOL;
                } else if ($column['typeName'] == 'INTEGER') {
                    $propertyAddList .= '                    ' .$object . '->' . $column['name'] . ' = (int) $formData[\'' . $inputName . '\'];' . PHP_EOL;
                    $propertyEditList .= '                    ' .$object . '->' . $column['name'] . ' = (int) $formData[\'' . $inputName . '\'];' . PHP_EOL;
                    $formDataEdit .= '        $formData[\'' . $inputName . '\'] = (int) ' . $object . '->' . $column['name'] . ';' . PHP_EOL;
                }
            }
        }

        // PARAMETER WITH CONSTANT
        foreach ($formData['fconstantable'] as $key => $value) {
            if ($value != '') {
                $constantArr = explode(',' , $value);
                $constant = array();
                foreach ($constantArr as $con) {
                    $conArr = explode(':', $con);
                    if (count($conArr) > 0) {
                        $constant[] = array(
                            'name' => $conArr[0],
                            'value' => $conArr[1],
                            'text' => $conArr[2]
                        );
                    }
                }
                $inputName = preg_replace('/_/', '', $key);
                $inputName = strtolower($inputName);
                $parameterTemplate .= "\n\t\t\t'{$inputName}List' => \\" . $s['{{MODULE_NAMESPACE}}'] . '\\' . $s['{{MODULE}}'] . '::get' . ucfirst($key) . 'List(),';
            }
        }

        foreach ($formData['finputtype'] as $key => $inputType) {
            $inputName = preg_replace('/_/', '', $key);
            $inputName = strtolower($inputName);

            if ($inputType == 'select') { // PARAMTER WITH GET OBJECT LIST
                $modelName = $formData['fselectmodel'][$key];
                $condition = $formData['fselectcondition'][$key];
                if (!empty($condition)) {
                    $parameterTemplate .= "\n\t\t\t'{$inputName}List' => \\" . $s['{{MODULE_NAMESPACE}}'] . '\\' . $modelName . '::find("' . $condition . '"),';
                } else {
                    $parameterTemplate .= "\n\t\t\t'{$inputName}List' => \\" . $s['{{MODULE_NAMESPACE}}'] . '\\' . $modelName . '::find(),';
                }                
            } elseif ($inputType == 'imageupload') { // FILE UPLOAD
                // GEN SETTING UPLOAD
                $settingFile = ROOT_PATH . '/app/config/setting.php';
                $settingContent = file_get_contents($settingFile);
                $settingContent = preg_replace('/\]\);/', '', $settingContent);
                $settingContent = trim($settingContent);
                //$multiple = $formData['fmultiimage'][$key];
                $propertyforimagename = $formData['fimagename'][$key];
                // If not exists setting of MODULE
                if (!isset($this->setting[$s['{{MODULE_LOWER}}']])) {
                    $settingContent .= "\n\t" . '\'' . $s['{{MODULE_LOWER}}'] . '\' => [';
                    $settingContent .= "\n\t\t" . '\'viewUrl\'                   =>  \'public/uploads/' . $s['{{MODULE_LOWER}}'] . '/\',';
                    $settingContent .= "\n\t\t" . '\'imageDirectory\'            =>  ROOT_PATH . \'/public/uploads/' . $s['{{MODULE_LOWER}}'] . '/\',';
                    $settingContent .= "\n\t\t" . '\'validExtension\'            =>  [\'JPG\', \'JPEG\', \'PNG\', \'GIF\'],';
                    $settingContent .= "\n\t\t" . '\'validMaxFileSize\'          =>  10 * 1024 * 1024, //size in byte';
                    $settingContent .= "\n\t\t" . '\'imageMaxWidth\'             =>  \'1200\',';
                    $settingContent .= "\n\t\t" . '\'imageMaxHeight\'            =>  \'1200\',';
                    $settingContent .= "\n\t\t" . '\'imageMediumWidth\'          =>  \'540\',';
                    $settingContent .= "\n\t\t" . '\'imageMediumHeight\'         =>  \'1000\',';
                    $settingContent .= "\n\t\t" . '\'imageThumbWidth\'           =>  \'300\',';
                    $settingContent .= "\n\t\t" . '\'imageThumbHeight\'          =>  \'200\',';
                    $settingContent .= "\n\t\t" . '\'imageThumbRatio\'           =>  \'3:2\',';
                    $settingContent .= "\n\t\t" . '\'imageQuality\'              =>  \'95\'';
                    $settingContent .= "\n\t" . '],';
                    $settingContent .= "\n]);";
                    if (file_put_contents($settingFile, $settingContent) !== false) {
                        $success[] = 'Saved file <code>' . $settingFile . '</code> successfully.';
                    } else {
                        $error[] = 'Error while saving file <code>' . $settingFile . '</code>.';
                    }
                }

                // GEN UPLOAD FUNCTION        
                $imageUploadFunction .= "\n\t" . 'public function uploadImage($file, $name, $id)';
                $imageUploadFunction .= "\n\t" . '{';
                $imageUploadFunction .= "\n\t\t" . '$curDateDir = Helper::getCurrentDateDirName();';
                $imageUploadFunction .= "\n\t\t" . '$extPart = substr(strrchr($file[\'name\'], \'.\'),1);';
                $imageUploadFunction .= "\n\t\t" . '$namePart =  Helper::codau2khongdau($name, true) . \'-\' . $id . time();';
                $imageUploadFunction .= "\n\t\t" . '$filename = $namePart . \'.\' . $extPart;';
                $imageUploadFunction .= "\n\n\t\t" . '$uploader = new Uploader($file[\'tmp_name\'], $filename, $this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageDirectory . $curDateDir, \'\');';
                $imageUploadFunction .= "\n\n\t\t" . '$uploadError = $uploader->upload(false, $filename);';
                $imageUploadFunction .= "\n\n\t\t" . 'if($uploadError != Uploader::ERROR_UPLOAD_OK) {';
                $imageUploadFunction .= "\n\t\t\t" . 'return $uploadError;';
                $imageUploadFunction .= "\n\t\t" . '} else {';
                $imageUploadFunction .= "\n\t\t\t" . '//Resize big image if needed';
                $imageUploadFunction .= "\n\t\t\t" . '$myImageResizer = new ImageResizer($this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageDirectory . $curDateDir, $filename, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageDirectory . $curDateDir, $filename, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageMaxWidth, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageMaxHeight, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '\'\', ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageQuality);';
                $imageUploadFunction .= "\n\t\t\t" . '$myImageResizer->output();';
                $imageUploadFunction .= "\n\t\t\t" . 'unset($myImageResizer);';
                $imageUploadFunction .= "\n\n\t\t\t" . '//Create medium image';
                $imageUploadFunction .= "\n\t\t\t" . '$nameMediumPart = substr($filename, 0, strrpos($filename, \'.\'));';
                $imageUploadFunction .= "\n\t\t\t" . '$nameMedium = $nameMediumPart . \'-medium.\' . $extPart;';
                $imageUploadFunction .= "\n\t\t\t" . '$myImageResizer = new ImageResizer($this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageDirectory . $curDateDir, $filename, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageDirectory . $curDateDir, $nameMedium, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageMediumWidth, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageMediumHeight, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '\'\', ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageQuality);';
                $imageUploadFunction .= "\n\t\t\t" . '$myImageResizer->output();';
                $imageUploadFunction .= "\n\t\t\t" . 'unset($myImageResizer);';
                $imageUploadFunction .= "\n\n\t\t\t" . '//Create thum image';
                $imageUploadFunction .= "\n\t\t\t" . '$nameThumbPart = substr($filename, 0, strrpos($filename, \'.\'));';
                $imageUploadFunction .= "\n\t\t\t" . '$nameThumb = $nameThumbPart . \'-small.\' . $extPart;';
                $imageUploadFunction .= "\n\t\t\t" . '$myImageResizer = new ImageResizer($this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageDirectory . $curDateDir, $filename, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageDirectory . $curDateDir, $nameThumb, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageThumbWidth, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageThumbHeight, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageThumbRatio, ';
                $imageUploadFunction .= "\n\t\t\t\t\t\t\t\t\t\t\t\t" . '$this->setting->' . $s['{{MODULE_LOWER}}'] . '->imageQuality);';
                $imageUploadFunction .= "\n\t\t\t" . '$myImageResizer->output();';
                $imageUploadFunction .= "\n\t\t\t" . 'unset($myImageResizer);';
                $imageUploadFunction .= "\n\n\t\t\t" . '//update database';
                $imageUploadFunction .= "\n\t\t\t" . '$filepath = $curDateDir . $filename;';
                $imageUploadFunction .= "\n\t\t\t" . '$my' . $s['{{MODULE}}'] . ' = \\' . $s['{{MODULE_NAMESPACE}}'] . '\\' . $s['{{MODULE}}'] . '::findFirst([';
                $imageUploadFunction .= "\n\t\t\t\t" . '\'id = :id:\',';
                $imageUploadFunction .= "\n\t\t\t\t" . '\'bind\' => [\'id\' => $id]';
                $imageUploadFunction .= "\n\t\t\t" . ']);';
                $imageUploadFunction .= "\n\t\t\t" . '$my' . $s['{{MODULE}}'] . '->' . $key . ' = (string) $filepath;';
                $imageUploadFunction .= "\n\t\t\t" . '$my' . $s['{{MODULE}}'] . '->save();';
                $imageUploadFunction .= "\n\t\t" . '}';
                $imageUploadFunction .= "\n\t" . '}';

                // REARRAY $_FILE FUNCTION TO MULTIPLE UPLOAD
                /*if ($multiple == 1) {
                    $imageUploadFunction .= "\n\n\t" . 'public function reArrayFiles()';
                    $imageUploadFunction .= "\n\t" . '{';
                    $imageUploadFunction .= "\n\t\t" . '$file_ary = array();';
                    $imageUploadFunction .= "\n\t\t" . '$file_count = count($file_post[\'name\']);';
                    $imageUploadFunction .= "\n\t\t" . '$file_keys = array_keys($file_post);';
                    $imageUploadFunction .= "\n\t\t" . 'for ($i=0; $i < $file_count; $i++) {';
                    $imageUploadFunction .= "\n\t\t\t" . 'foreach ($file_keys as $key) {';
                    $imageUploadFunction .= "\n\t\t\t\t" . '$file_ary[$i][$key] = $file_post[$key][$i];';
                    $imageUploadFunction .= "\n\t\t\t" . '}';
                    $imageUploadFunction .= "\n\t\t" . '}';
                    $imageUploadFunction .= "\n\t\t" . 'return $file_ary;';
                    $imageUploadFunction .= "\n\t" . '}';
                }*/

                $imageUploadAdd .= '// Upload Image Process';
                $imageUploadAdd .= "\n\t\t\t\t\t\t" . 'if (strlen($_FILES[\'' . $inputName . '\'][\'name\']) > 0) {';
                $imageUploadAdd .= "\n\t\t\t\t\t\t\t" . '$file = $_FILES[\'' . $inputName . '\'];';
                $imageUploadAdd .= "\n\t\t\t\t\t\t\t" . '$this->uploadImage($file, $my' . $s['{{MODULE}}'] . '->' . $propertyforimagename . ', $my' . $s['{{MODULE}}'] . '->id);';
                $imageUploadAdd .= "\n\t\t\t\t\t\t" . '}';

                $imageUploadEdit .= '// Upload Image Process';
                $imageUploadEdit .= "\n\t\t\t\t\t\t" . 'if (strlen($_FILES[\'' . $inputName . '\'][\'name\']) > 0) {';
                $imageUploadEdit .= "\n\t\t\t\t\t\t\t" . '$file = $_FILES[\'' . $inputName . '\'];';
                $imageUploadEdit .= "\n\t\t\t\t\t\t\t" . '$this->uploadImage($file, $my' . $s['{{MODULE}}'] . '->' . $propertyforimagename . ', $my' . $s['{{MODULE}}'] . '->id);';
                $imageUploadEdit .= "\n\t\t\t\t\t\t" . '}';
            }
        }

        $s['{{PROPERTY_ADD_LIST}}'] = $propertyAddList;
        $s['{{PROPERTY_EDIT_LIST}}'] = $propertyEditList;
        $s['{{FORMDATA_EDIT_LIST}}'] = $formDataEdit;
        $s['{{PARAMETER_TEMPLATE_LIST}}'] = $parameterTemplate;
        $s['{{IMAGE_UPLOAD_FUNCTION}}'] = $imageUploadFunction;
        $s['{{IMAGE_UPLOAD_ADD}}'] = $imageUploadAdd;
        $s['{{IMAGE_UPLOAD_EDIT}}'] = $imageUploadEdit;

        if (is_dir($dir) && is_writable($dir)) {

            $destpath = $dir . $formData['fcontrollerclass'] . '.php';
            $search = array_keys($s);
            $replacement = array_values($s);

            $sourceContent = file_get_contents($source['controlleradmin']);

            $sourceContent = str_replace($search, $replacement, $sourceContent);
            
            if (file_put_contents($destpath, $sourceContent) !== false) {
                $success[] = 'Saved file <code>'.$destpath.'</code> successfully.';
            } else {
                $error[] = 'Error while saving file <code>'. $destpath . '</code>.';
            }
        }

        return $valid;
    }

    private function genT($formData, &$error, &$success)
    {
        $valid = true;
        $dir =  $this->config->application->viewsDir . 'admin/';
        $s = array();
        $moduleDir = $dir . strtolower($formData['fmodel']);

        $source_basedir = $this->config['application']['viewsDir'] . 'admin/generator/generate_format/';
        $source['controlleradminindex'] = $source_basedir . 'controller_admin_index.volt';
        $source['controlleradminadd'] = $source_basedir . 'controller_admin_add.volt';
        $source['controlleradminedit'] = $source_basedir . 'controller_admin_edit.volt';
        
        $s['{{MODULE}}'] = ucfirst($formData['fmodel']);
        $s['{{MODULE_LOWER}}'] = strtolower($formData['fmodel']);
        $s['{{FA_ICON}}'] = $formData['fcontrollericonclass'];

        // =================================================================
        // Replacement index template
        // =================================================================
        $tableHead = "<th width=\"40\"><input type=\"checkbox\" class=\"check-all\"/></th>\n";
        $tableBody = "{% if page.items.count() > 0 %}\n";
        $tableBody .= "                {% for item in page.items %}\n";
        $tableBody .= "                    <tr>\n";
        $tableBody .= "                        <td><input type=\"checkbox\" name=\"fbulkid[]\" value=\"{{ item.id }}\" {% if formData['fbulkid'] is defined %}{% for key, value in formData['fbulkid'] if value == item.id %}checked=\"checked\"{% endfor %}{% endif %} /></td>\n";
        $colSpan = 1;
        $excludeindex = array();
        if (isset($formData['fexcludeindex'])) {
            $excludeindex = array_keys($formData['fexcludeindex']);
        }
        // fix warning not exists formData
        if (!isset($formData['fsortable'])) {
            $formData['fsortable'] = array();
        }
        $sortable = array();
        if (isset($formData['fsortable'])) {
            $sortable = array_keys($formData['fsortable']);
        }
        $i = 2;
        foreach ($formData['columns'] as $key => $column) {
            $inputName = preg_replace('/_/', '', $column['name']);
            $columnName = ucfirst($inputName);
            $inputName = strtolower($inputName);

            $label = "{{ lang._('Label{$columnName}') }}";
            // if field is not exclude index
            if (!in_array($column['name'], $excludeindex)) {
                // if field is sortable
                if (in_array($column['name'], $sortable)) {
                    $label = '<a href="{{ config.application.baseUriAdmin }}' . $s['{{MODULE_LOWER}}'] 
                           . '?sortby=' . $inputName . '&sorttype={% if formData[\'sorttype\']|upper == \'DESC\'%}ASC'
                           . '{% else %}DESC{% endif %}{% if formData[\'keyword\'] is defined %}&keyword='
                           . '{{ formData[\'keyword\'] }}{% endif %}">' . $label . '</a>';
                }
                $tableHead .= "\t\t\t\t\t<th id=\"" . $column['name'] . "\">" . $label . "</th>\n";
                $item = '{{ item.' . $column['name'] . ' }}';
                if ($column['typeName'] == 'VARCHAR' || $column['typeName'] == 'TEXT') {
                    $item = '{{ item.' . $column['name'] . '|escape }}';
                }
                $tableBody .= "                        <td>{$item}</td>\n";
                $colSpan++;
            }
            $i++;
        }
        $tableHead .= "\t\t\t\t\t<th width=\"100\"></th>".PHP_EOL;
        $s['{{TABLE_HEAD}}'] = $tableHead;

        $tableBody .= "                        <td>\n";
        $tableBody .= "                            <div class=\"btn-group pull-right\">\n";
        $tableBody .= "                                <a href=\"{{ config.application.baseUriAdmin }}" . $s['{{MODULE_LOWER}}']  . "/edit/{{ item.id }}\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-cog\"></i></a>\n";
        $tableBody .= "                                <a href=\"javascript:delm('{{ config.application.baseUriAdmin }}" . $s['{{MODULE_LOWER}}']  . "/delete/{{ item.id }}/{{ redirectUrl }}');\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-trash-o\"></i></a>\n";
        $tableBody .= "                            </div>\n";
        $tableBody .= "                        </td>\n";
        $tableBody .= "                    </tr>\n";
        $tableBody .= "                {% endfor %}\n";
        $tableBody .= "                {% else %}\n";
        $tableBody .= "                <tr>\n";
        $tableBody .= "                    <td colspan=\"" . $colSpan . "\"> Data Notfound!</td>\n";
        $tableBody .= "                </tr>\n";
        $tableBody .= "                {% endif %}\n";
        $s['{{TABLE_BODY}}'] = $tableBody;
        $s['{{COLSPAN_FOOTER}}'] = $i;

        // =================================================================
        // Replacement add & edit template
        // =================================================================
        $formAddControlGroup = "\n";
        $formEditControlGroup = "\n";

        $constantName = array();
        foreach ($formData['fconstantable'] as $key => $value) {
            if ($value != '') {
                $constantArr = explode(',' , $value);
                foreach ($constantArr as $con) {
                    $conArr = explode(':', $con);
                    if (count($conArr) > 0) {
                        $constantName[] = $key;
                    }
                }
            }
        }

        $exclude = array();
        if (isset($formData['fexclude'])) {
            $exclude = array_keys($formData['fexclude']);
        }
        $exclude[] = 'id';
        $exclude[] = 'created_at';
        $exclude[] = 'updated_at';
        foreach ($formData['columns'] as $key => $column) {
            $inputName = preg_replace('/_/', '', $column['name']);
            $columnName = ucfirst($inputName);
            $inputName = strtolower($inputName);

            $label = "{{ lang._('Label{$columnName}') }}";
            // if field is not exclude index
            if (!in_array($column['name'], $exclude)) {
                $inputType = $formData['finputtype'][$column['name']];
                $requiredIcon = $formData['fvalidating'][$column['name']] == 'notempty' ? '<span class="star_require">*</span>' : '';

                $colmid = $inputType == 'editor' ? '12' : '6';

                $formAddControlGroup .= "                <div class=\"col-md-{$colmid} ssb clear inner-left\">\n";
                $formAddControlGroup .= "                    <label for=\"{$inputName}\">$label {$requiredIcon}</label>\n";

                $formEditControlGroup .= "                <div class=\"col-md-{$colmid} ssb clear inner-left\">\n";
                $formEditControlGroup .= "                    <label for=\"{$inputName}\">$label {$requiredIcon}</label>\n";

                if (in_array($column['name'], $constantName)) {
                    $formAddControlGroup .= "                    <select id=\"{$inputName}\" name=\"{$inputName}\" class=\"col-md-12\">\n";
                    $formAddControlGroup .= "                        <option value=\"0\">- - - -</option>\n";
                    $formAddControlGroup .= "                        {% for key, {$inputName} in {$inputName}List %}\n";
                    $formAddControlGroup .= "                            <option value=\"{{ key }}\" {% if formData['{$inputName}'] is defined and formData['{$inputName}'] == key %}selected=\"selected\"{% endif %}>{{ {$inputName} }}</option>\n";
                    $formAddControlGroup .= "                        {% endfor %}\n";
                    $formAddControlGroup .= "                    </select>\n";

                    $formEditControlGroup .= "                    <select id=\"{$inputName}\" name=\"{$inputName}\" class=\"col-md-12\">\n";
                    $formEditControlGroup .= "                        <option value=\"0\">- - - -</option>\n";
                    $formEditControlGroup .= "                        {% for key, {$inputName} in {$inputName}List %}\n";
                    $formEditControlGroup .= "                            <option value=\"{{ key }}\" {% if formData['{$inputName}'] is defined and formData['{$inputName}'] == key %}selected=\"selected\"{% endif %}>{{ {$inputName} }}</option>\n";
                    $formEditControlGroup .= "                        {% endfor %}\n";
                    $formEditControlGroup .= "                    </select>\n";
                } else {
                    if ($inputType == 'text') {
                        $formAddControlGroup .= "                    <input type=\"text\" name=\"{$inputName}\" id=\"{$inputName}\" value=\"{% if formData['{$inputName}'] is defined %}{{ formData['{$inputName}'] }}{% endif %}\"/>\n";

                        $formEditControlGroup .= "                    <input type=\"text\" name=\"{$inputName}\" id=\"{$inputName}\" value=\"{% if formData['{$inputName}'] is defined %}{{ formData['{$inputName}'] }}{% endif %}\"/>\n";
                    } elseif ($inputType == 'textarea') {
                        $formAddControlGroup .= "                    <textarea class=\"form-control\" name=\"{$inputName}\" id=\"{$inputName}\">{% if formData['{$inputName}'] is defined %}{{ formData['{$inputName}'] }}{% endif %}</textarea>\n";

                        $formEditControlGroup .= "                    <textarea class=\"form-control\" name=\"{$inputName}\" id=\"{$inputName}\">{% if formData['{$inputName}'] is defined %}{{ formData['{$inputName}'] }}{% endif %}</textarea>\n";
                    } elseif ($inputType == 'select') {
                        $selectValue = $formData['fselectvalue'][$column['name']];
                        $selectText = $formData['fselecttext'][$column['name']];

                        $formAddControlGroup .= "                    <select id=\"{$inputName}\" name=\"{$inputName}\" class=\"col-md-12\">\n";
                        $formAddControlGroup .= "                        <option value=\"0\">- - - -</option>\n";
                        $formAddControlGroup .= "                        {% for {$inputName} in {$inputName}List %}\n";
                        $formAddControlGroup .= "                            <option value=\"{{ {$inputName}.{$selectValue} }}\" {% if formData['{$inputName}'] is defined and formData['{$inputName}'] == {$inputName}.{$selectValue} %}selected=\"selected\"{% endif %}>{{ {$inputName}.{$selectText} }}</option>\n";
                        $formAddControlGroup .= "                        {% endfor %}\n";
                        $formAddControlGroup .= "                    </select>\n";

                        $formEditControlGroup .= "                    <select id=\"{$inputName}\" name=\"{$inputName}\" class=\"col-md-12\">\n";
                        $formEditControlGroup .= "                        <option value=\"0\">- - - -</option>\n";
                        $formEditControlGroup .= "                        {% for {$inputName} in {$inputName}List %}\n";
                        $formEditControlGroup .= "                            <option value=\"{{ {$inputName}.{$selectValue} }}\" {% if formData['{$inputName}'] is defined and formData['{$inputName}'] == {$inputName}.{$selectValue} %}selected=\"selected\"{% endif %}>{{ {$inputName}.{$selectText} }}</option>\n";
                        $formEditControlGroup .= "                        {% endfor %}\n";
                        $formEditControlGroup .= "                    </select>\n";
                    } elseif ($inputType == 'imageupload') {
                        $formAddControlGroup .= "                    <input type=\"file\" name=\"{$inputName}\" id=\"{$inputName}\" />\n";

                        $formEditControlGroup .= "                    <input type=\"file\" name=\"{$inputName}\" id=\"{$inputName}\" />\n";
                    } else {
                        $formAddControlGroup .= "                    <textarea class=\"summernote-editor\" name=\"{$inputName}\" id=\"summernote-editor\">{% if formData['{$inputName}'] is defined %}{{ formData['{$inputName}'] }}{% endif %}</textarea>\n";

                        $formEditControlGroup .= "                    <textarea class=\"summernote-editor\" name=\"{$inputName}\" id=\"summernote-editor\">{% if formData['{$inputName}'] is defined %}{{ formData['{$inputName}'] }}{% endif %}</textarea>\n";
                    }

                }

                $formAddControlGroup .= "                </div>\n";

                $formEditControlGroup .= "                </div>\n";
            }
        }
        $s['{{FORM_ADD_CONTROLGROUP}}'] = $formAddControlGroup;
        $s['{{FORM_EDIT_CONTROLGROUP}}'] = $formEditControlGroup;

        if (!is_dir($moduleDir)) {
            mkdir($moduleDir);
        }

        $destIndexPath = $moduleDir . '/index.volt';
        $destAddPath = $moduleDir . '/add.volt';
        $destEditPath = $moduleDir . '/edit.volt';
        $search = array_keys($s);
        $replacement = array_values($s);

        $sourceIndexContent = file_get_contents($source['controlleradminindex']);
        $sourceIndexContent = str_replace($search, $replacement, $sourceIndexContent);

        if (file_put_contents($destIndexPath, $sourceIndexContent) !== false) {
            $success[] = 'Saved file <code>' . $destIndexPath . '</code> successfully.';
        } else {
            $error[] = 'Error while saving file <code>' . $destIndexPath . '</code>.';
        }

        $sourceAddContent = file_get_contents($source['controlleradminadd']);
        $sourceAddContent = str_replace($search, $replacement, $sourceAddContent);
        
        if (file_put_contents($destAddPath, $sourceAddContent) !== false) {
            $success[] = 'Saved file <code>'.$destAddPath.'</code> successfully.';
        } else {
            $error[] = 'Error while saving file <code>'. $destAddPath . '</code>.';
        }

        $sourceEditContent = file_get_contents($source['controlleradminedit']);
        $sourceEditContent = str_replace($search, $replacement, $sourceEditContent);
        
        if (file_put_contents($destEditPath, $sourceEditContent) !== false) {
            $success[] = 'Saved file <code>'.$destEditPath.'</code> successfully.';
        } else {
            $error[] = 'Error while saving file <code>'. $destEditPath . '</code>.';
        }


        return $valid;
    }

    public function genL($formData, &$error, &$success)
    {
        $valid = true;
        $dir =  __DIR__ . '/../../language/';
        $destENPath = $dir . 'en/admin/' . strtolower($formData['fmodel']) . '.php';
        $destVNPath = $dir . 'vn/admin/' . strtolower($formData['fmodel']) . '.php';
        $s = array();
        $moduleDir = $dir . strtolower($formData['fmodel']);

        // CREATE directory LANG
        if (!is_dir($dir . 'en/admin/')) {
            mkdir($dir . 'en/admin/', 0777);
        }
        if (!is_dir($dir . 'vn/admin/')) {
            mkdir($dir . 'vn/admin/', 0777);
        }

        $source_basedir = $this->config['application']['viewsDir'] . 'admin/generator/generate_format/';
        $source['languageadmin'] = $source_basedir . 'language_admin.volt';
        
        $s['{{MODULE}}'] = ucfirst($formData['fmodel']);
        $s['{{MODULE_LOWER}}'] = strtolower($formData['fmodel']);

        // =================================================================
        // Replacement lang en
        // =================================================================
        $s['{{INDEX_ADD_BUTTON}}'] = 'ADD';
        $s['{{TAB_LISTING}}'] = 'Listing';
        $s['{{ADD_TITLE}}'] = 'Add';
        $s['{{ADD_BUTTON}}'] = 'ADD';
        $s['{{EDIT_TITLE}}'] = 'Edit';
        $s['{{EDIT_BUTTON}}'] = 'UPDATE';
        $s['{{CANCEL_BUTTON}}'] = 'Cancel';
        $s['{{OVER_VIEW_TITLE}}'] = $s['{{MODULE}}'] . ' Overview';
        $s['{{OVER_VIEW}}'] = 'General Information about ' . $s['{{MODULE}}'];
        $s['{{SUCCESS_INSERT_DATA}}'] = $s['{{MODULE}}'] . ' created successfully.';
        $s['{{SUCCESS_UPDATE_DATA}}'] = $s['{{MODULE}}'] . ' updated successfully.';
        $s['{{SUCCESS_DELETE_DATA}}'] = $s['{{MODULE}}'] . ' with ID <strong>##ID##</strong> was deleted successfully.';
        $s['{{ERROR_INSERT_DATA}}'] = 'Have a problem while create new '. $s['{{MODULE_LOWER}}'] . ' process. Try again later';
        $s['{{ERROR_UPDATE_DATA}}'] = 'Have a problem while updating '. $s['{{MODULE_LOWER}}'] . ' process. Try again later';
        $s['{{ERROR_DELETE_DATA}}'] = 'Have a problem while deleting process with ID <strong>##ID##</strong>';
        $s['{{ERROR_TOKEN_MISSMATCH}}'] = 'Token missmatch. Re enter page from browser toolbar and try again later.';
        $s['{{ERROR_NO_BULK_SELECTED}}'] = 'No bulk item selected.';
        $s['{{ERROR_NO_ACTION_SELECTED}}'] = 'No Action Selected.';
        $s['{{REQUIRED}}'] = 'Required';

        $fieldLabel = "\n";
        $validateMessage = "\n";
        foreach ($formData['columns'] as $key => $column) {
            $columnName = preg_replace('/_/', '', $column['name']);
            $columnName = ucfirst($columnName);
            $labelName = preg_replace('/_/', ' ', $column['name']);
            $labelName = ucfirst($labelName);
            $fieldLabel .= "    'Label{$columnName}' => '{$labelName}',\n";
            $validate = $formData['fvalidating'][$column['name']];
            $inputType = $formData['finputtype'][$column['name']];
            switch ($validate) {
                case 'notempty':
                case 'uploadrequied':
                    $validateMessage .= "    '{$columnName}IsRequired' => '{$columnName} is required.',\n";
                    break;
                case 'greaterthanzero':
                    $validateMessage .= "    '{$columnName}GreaterThanZero' => '{$columnName} is greater than zero.',\n";
                    break;
                case 'email':
                    $validateMessage .= "    '{$columnName}IsEmail' => '{$columnName} is not an Email Address.',\n";
                    break;
            }

            if ($inputType == 'imageupload') {
                $validateMessage .= "    'FileTypeNotValid' => 'File type invalid.',\n";
                $validateMessage .= "    'FileSizeNotValid' => 'File size invalid.',\n";
            }
        }
        $s['{{FIELD_LABELS}}'] = $fieldLabel;
        $s['{{VALIDATE_MESSAGE}}'] = $validateMessage;

        $search = array_keys($s);
        $replacement = array_values($s);

        $sourceENContent = file_get_contents($source['languageadmin']);
        $sourceENContent = str_replace($search, $replacement, $sourceENContent);
        
        if (file_put_contents($destENPath, $sourceENContent) !== false) {
            $success[] = 'Saved file <code>'.$destENPath.'</code> successfully.';
        } else {
            $error[] = 'Error while saving file <code>'. $destENPath . '</code>.';
        }

        // =================================================================
        // Replacement lang vn
        // =================================================================
        $langVN = [
            'title' => 'Tiêu đề',
            'name' => 'Tên',
            'summary' => 'Tóm tắt',
            'content' => 'Nội dung',
            'password' => 'Mật khẩu',
            'author' => 'Tác giả',
            'status' => 'Trạng Thái',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
            'position' => 'Vị trí',
            'displayorder' => 'Thứ tự hiển thị'
        ];

        $s['{{INDEX_ADD_BUTTON}}'] = 'THÊM';
        $s['{{TAB_LISTING}}'] = 'Danh sách';
        $s['{{ADD_TITLE}}'] = 'Thêm mới';
        $s['{{ADD_BUTTON}}'] = 'Thêm mới';
        $s['{{EDIT_TITLE}}'] = 'Cập nhật';
        $s['{{EDIT_BUTTON}}'] = 'Cập nhật';
        $s['{{CANCEL_BUTTON}}'] = 'Hủy';
        $s['{{OVER_VIEW_TITLE}}'] = 'Tổng quan về ' . $s['{{MODULE}}'];
        $s['{{OVER_VIEW}}'] = 'Thông tin chung về ' . $s['{{MODULE}}'];
        $s['{{SUCCESS_INSERT_DATA}}'] = $s['{{MODULE}}'] . ' Thêm mới thành công.';
        $s['{{SUCCESS_UPDATE_DATA}}'] = $s['{{MODULE}}'] . ' Cập nhật thành công.';
        $s['{{SUCCESS_DELETE_DATA}}'] = $s['{{MODULE}}'] . ' with ID <strong>##ID##</strong> đã được xóa thành công.';
        $s['{{ERROR_INSERT_DATA}}'] = 'Có lỗi trong quá trình tạo mới '. $s['{{MODULE_LOWER}}'] . '. Thử lại lần sau.';
        $s['{{ERROR_UPDATE_DATA}}'] = 'Có lỗi trong quá trình cập nhật '. $s['{{MODULE_LOWER}}'] . '. Thử lại lần sau.';
        $s['{{ERROR_DELETE_DATA}}'] = 'Có lỗi trong quá trình xóa '. $s['{{MODULE_LOWER}}'] . ' với ID <strong>##ID##</strong>. Thử lại lần sau.';
        $s['{{ERROR_TOKEN_MISSMATCH}}'] = 'Token không trùng khớp. Truy cập lại trang và thử lại.';
        $s['{{ERROR_NO_BULK_SELECTED}}'] = 'Không có đối tượng nào được chọn.';
        $s['{{ERROR_NO_ACTION_SELECTED}}'] = 'Không có hành động nào được chọn.';
        $s['{{REQUIRED}}'] = 'Không bỏ trống';

        $fieldLabel = "\n";
        $validateMessage = "\n";
        foreach ($formData['columns'] as $key => $column) {
            $columnName = preg_replace('/_/', '', $column['name']);
            $columnName = ucfirst($columnName);
            $colName = strtolower($column['name']);
            $labelName = preg_replace('/_/', ' ', $column['name']);
            $labelName = ucfirst($labelName);
            if (isset($langVN[$colName])) {
                $labelName = $langVN[$colName];
            }
            $fieldLabel .= "    'Label{$columnName}' => '{$labelName}',\n";
            $validate = $formData['fvalidating'][$column['name']];
            $inputType = $formData['finputtype'][$column['name']];
            switch ($validate) {
                case 'notempty':
                case 'uploadrequied':
                    $validateMessage .= "    '{$columnName}IsRequired' => 'Bạn chưa nhập {$columnName}.',\n";
                    break;
                case 'greaterthanzero':
                    $validateMessage .= "    '{$columnName}GreaterThanZero' => '{$columnName} phải lớn hơn 0.',\n";
                    break;
                case 'email':
                    $validateMessage .= "    '{$columnName}IsEmail' => '{$columnName} phải là địa chỉ mail.',\n";
                    break;
            }

            if ($inputType == 'imageupload') {
                $validateMessage .= "    'FileTypeNotValid' => 'File có định dạng không hợp lệ.',\n";
                $validateMessage .= "    'FileSizeNotValid' => 'File có kích thước không cho phép.',\n";
            }
        }
        $s['{{FIELD_LABELS}}'] = $fieldLabel;
        $s['{{VALIDATE_MESSAGE}}'] = $validateMessage;

        $search = array_keys($s);
        $replacement = array_values($s);
        
        $sourceVNContent = file_get_contents($source['languageadmin']);
        $sourceVNContent = str_replace($search, $replacement, $sourceVNContent);
        
        if (file_put_contents($destVNPath, $sourceVNContent) !== false) {
            $success[] = 'Saved file <code>' . $destVNPath . '</code> successfully.';
        } else {
            $error[] = 'Error while saving file <code>'. $destVNPath . '</code>.';
        }
    }

    public function getcolumntableAction()
    {
        $columns = array();
        $modelName = $_POST['model'];
        $columns = $this->getColumnByModel($modelName);

        header('Content-Type:text/json');
        echo json_encode($columns);
    }

    public function getColumnByModel($modelName)
    {
        $columns = array();

        $class = '\\Model\\' . $modelName;

        $object = new $class();
        $table = $object->getSource();
        $fields = $this->db->describeColumns($table);
        
        foreach ($fields as $field) {
            $columns[] = [
                'name'      =>  $field->getName()
            ];
        }

        return $columns;
    }
}