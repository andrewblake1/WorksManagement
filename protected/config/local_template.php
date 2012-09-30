<?php

/**
 * Local configuration override.
 * Use this to override elements in the config array (combined from main.php and mode_x.php)
 * NOTE: When using a version control system, do NOT commit this file to the repository.
 */

return array(
	// Set yiiPath (relative to Environment.php)
	'yiiPath' => dirname(__FILE__) . '/../../../../../../var/yii/yii/framework/yii.php',
	'yiicPath' => dirname(__FILE__) . '/../../../../../../var/yii/yii/framework/yiic.php',
	'yiitPath' => dirname(__FILE__) . '/../../../../../../var/yii/yii/framework/yiit.php',

	// Set YII_DEBUG and YII_TRACE_LEVEL flags
	'yiiDebug' => true,
	'yiiTraceLevel' => 3,
	
	// This is the specific Web application configuration for this mode.
	// Supplied config elements will be merged into the main config array.
	'configWeb' => array(

		// Application components
		'components' => array(
			// for test.wce.co.nz
			// Database
			'db'=>array(
				'connectionString' => 'mysql:host=localhost;dbname=',
				'emulatePrepare' => true,
				'username' => '',
				'password' => '',
				'charset' => 'utf8',
			),

		),
		
		'params'=>array(
			// directory to store uploaded files in that are below document root
			'privateUploadPath' => '/home/www-data/uploads/test/melbourne/',
			// directory to store uploaded files in that are above document root
			'publicUploadPath' => dirname(__FILE__) . '/../../assets/',
			// web directory to store uploaded files in that are above document root
			'webUploadPath' => '/assets/',
		),

	),

);