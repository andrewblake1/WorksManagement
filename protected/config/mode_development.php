<?php

/**
 * Development configuration
 * Usage:
 * - Local website
 * - Local DB
 * - Show all details on each error
 * - Gii module enabled
 */
// TODO: AB examine correct error reporting - should fix up to strict level ideally
//ini_set('error_reporting', E_NOTICE);

return array(

	// Set yiiPath (relative to Environment.php)
	//'yiiPath' => dirname(__FILE__) . '/../../../yii/framework/yii.php',
	//'yiicPath' => dirname(__FILE__) . '/../../../yii/framework/yiic.php',
	//'yiitPath' => dirname(__FILE__) . '/../../../yii/framework/yiit.php',

	// Set YII_DEBUG and YII_TRACE_LEVEL flags
	'yiiDebug' => true,
//	'yiiDebug' => false,
	'yiiTraceLevel' => 3,
//	'yiiTraceLevel' => 0,
	
	// Static function Yii::setPathOfAlias()
	'yiiSetPathOfAlias' => array(
		// uncomment the following to define a path alias
		//'local' => 'path/to/local-folder'
	),

	// This is the specific Web application configuration for this mode.
	// Supplied config elements will be merged into the main config array.
	'configWeb' => array(

		// Modules
		'modules' => array(
			'gii'=>array(
				'class'=>'system.gii.GiiModule',
				'password' => false,
				// If removed, Gii defaults to localhost only. Edit carefully to taste.
				'ipFilters'=>array('127.0.0.1','::1'),
				'generatorPaths'=>array(
					'bootstrap.gii', // since 0.9.1
					'application.gii'// nested set  Model and Crud templates
				),
			),
		),

		// Application components
		'components' => array(

			// Database
			'db'=>array(
				'connectionString' => 'mysql:host=localhost;dbname=worksmanagement_dev',
				'emulatePrepare' => true,
				'username' => 'root',
				'password' => 'root',
				'charset' => 'utf8',
			),

			// Application Log
			'log' => array(
				'class' => 'CLogRouter',
				'routes' => array(
					array(
						'class'=>'CWebLogRoute',
						'levels'=>'trace',
						'categories'=>'vardump',
						'showInFireBug'=>true
					),
/*					// Save log messages on file
					array(
						'class' => 'CFileLogRoute',
						'levels' => 'error, warning, trace, info',
					),
					// Show log messages on web pages
					array(
						'class' => 'CWebLogRoute',
						//'categories' => 'system.db.CDbCommand', //queries
						'levels' => 'error, warning, trace, info',
						//'showInFireBug' => true,
					),*/
				),
			),

		),

	),
	
	// This is the Console application configuration. Any writable
	// CConsoleApplication properties can be configured here.
    // Leave array empty if not used.
    // Use value 'inherit' to copy from generated configWeb.
	'configConsole' => array(
	),

);