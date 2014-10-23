<?php
/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/
namespace Phalcon\Translate\Adapter;

use Phalcon\Translate\Adapter;
use Phalcon\Translate\AdapterInterface;
use Phalcon\Translate\Exception;

class Mynative extends Adapter implements AdapterInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * Class constructor.
     *
     * @param  array                        $options
     * @throws \Phalcon\Translate\Exception
     */
    public function __construct($options)
    {
        if (!isset($options['dirLang'])) {
            throw new Exception("Parameter 'dirLang' is required");
        }

        if (!isset($options['namespace'])) {
            throw new Exception("Parameter 'namespace' is required");
        }

        if (!isset($options['controller'])) {
            throw new Exception("Parameter 'controller' is required");
        }

        if (!isset($options['language'])) {
            throw new Exception("Parameter 'language' is required");
        }

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $index
     * @param  array  $placeholders
     * @return string
     */
    public function query($index, $placeholders = null)
    {
        $options = $this->options;

        $langPath = $options['dirLang'] . 
                    $options['language'] . '/' .
                    $options['namespace'] . '/' .
                    $options['controller'] . '.php';

        // Check if we have a translation file for that lang
        if (file_exists($langPath)) {
            require $langPath;
        } else {
          $langPath = $options['dirLang'] . 'en/' . $options['namespace'] . '/' . $options['controller'] . '.php';
          require $langPath;
        }

        //Return a translation object
        $obj = new \Phalcon\Translate\Adapter\NativeArray(array(
           'content' => $contents
        ));

        return $obj->_($index);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $index
     * @return boolean
     */
    public function exists($index)
    {
        $options = $this->options;
    }
}
