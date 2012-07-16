<?php

/**
 * Main configuration.
 * All properties can be overridden in mode_<mode>.php files
 */

return array(


	// Set yiiPath (relative to Environment.php)
	'yiiPath' => dirname(__FILE__) . '/../../../../yii-1.1.10.r3566/framework/yii.php',
	'yiicPath' => dirname(__FILE__) . '/../../../../yii-1.1.10.r3566/framework/yiic.php',
	'yiitPath' => dirname(__FILE__) . '/../../../../yii-1.1.10.r3566/framework/yiit.php',

	// Set YII_DEBUG and YII_TRACE_LEVEL flags
	'yiiDebug' => true,
	'yiiTraceLevel' => 0,

	// Static function Yii::setPathOfAlias()
	'yiiSetPathOfAlias' => array(
		// uncomment the following to define a path alias
		//'local' => 'path/to/local-folder'
	),

	// This is the main Web application configuration. Any writable
	// CWebApplication properties can be configured here.
	'configWeb' => array(

		'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
		'name' => 'Works Managment',
		'theme'=>'base',

		'preload'=>array(
			'log',			// preloading 'log' component
			'bootstrap',	// preload the bootstrap component
		),

		// Autoloading model and component classes
		'import' => array(
			'application.models.*',
			'application.components.*',
			'application.extensions.*',
			'ext.giix-components.*',	// giix components
		),
		
		// Application components
		'components' => array(
		
			'user' => array(
				// enable cookie-based authentication
				'allowAutoLogin' => true,
			),
			
			// uncomment the following to enable URLs in path-format
			'urlManager'=>array(
				'urlFormat'=>'path',
				'showScriptName'=>false,
				'rules'=>array(
					// removes 'site' & 'index' from urls and add pretty urls to static pages
					'contact'=>'site/contact',
					'page/<view:\w+>'=>'site/page',
					'/'=>'site/index',

					'<controller:\w+>/<id:\d+>'=>'<controller>/view',
					'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
					'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

					// add support for modules
					'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
				),
			),

			// RBAC
			'authManager'=>array(
				'class'=>'CDbAuthManager',
				'connectionID'=>'db',
			),
			
			// Database
			'db' => array(
				'connectionString' => '', //override in config/mode_<mode>.php
				'emulatePrepare' => true,
				'username' => '', //override in config/mode_<mode>.php
				'password' => '', //override in config/mode_<mode>.php
				'charset' => 'utf8',
			),

			// Error handler
			'errorHandler'=>array(
				// use 'site/error' action to display errors
				'errorAction'=>'site/error',
			),

			'bootstrap'=>array(
				'class'=>'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
			),
		),

		// application-level parameters that can be accessed
		// using Yii::app()->params['paramName']
		'params'=>array(
			// this is used in contact page
			'adminEmail'=>'webmaster@example.com',
			// delimiter - used when searching and autoseleting as break between fields
			'delimiter'=>array('search'=>'/', 'display'=>', '),
		),

	),

	// This is the Console application configuration. Any writable
	// CConsoleApplication properties can be configured here.
    // Leave array empty if not used.
    // Use value 'inherit' to copy from generated configWeb.
	'configConsole' => array(

		'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
		'name' => 'My Console Application',

		// Preloading 'log' component
		'preload' => array('log'),

		// Autoloading model and component classes
		'import'=>'inherit',

		// Application componentshome
		'components'=>array(

			// Database
			'db'=>'inherit',

			// Application Log
			'log' => array(
				'class' => 'CLogRouter',
				'routes' => array(
					// Save log messages on file
					array(
						'class' => 'CFileLogRoute',
						'levels' => 'error, warning, trace, info',
					),
				),
			),

		),

	),

);