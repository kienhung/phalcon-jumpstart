<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

return array(
	'jsSite' => array(
        '../js/site/main.js'
		),
   'jsAdmin' => array(
        '../plugins/bootstrap3Editable/js/bootstrap-editable.js',
        '../plugins/summernote/js/summernote.min.js',
        '../js/admin/admin.js',
		),
	'cssSite' => array(
        '../css/site/main.css'
        ),
	'cssAdmin' => array(
        '../css/admin/mystyle.css',
        '../css/admin/spf.css',
        '../plugins/summernote/css/summernote.css',
        '../plugins/bootstrap3Editable/css/bootstrap-editable.css',
        '../css/admin/admin.css',
		),
);
