<?php

/**
 * Production configuration
 * Usage:
 * - Online website
 * - Production DB
 * - Standard production error pages (404, 500, etc.)
 */
global $wceBusiness;

return array(
	
	// Set YII_DEBUG and YII_TRACE_LEVEL flags
	'yiiDebug' => false,
	'yiiTraceLevel' => 0,
	
	// Static function Yii::setPathOfAlias()
	'yiiSetPathOfAlias' => array(
		// uncomment the following to define a path alias
		//'local' => 'path/to/local-folder'
	),

	// This is the specific Web application configuration for this mode.
	// Supplied config elements will be merged into the main config array.
	'configWeb' => array(

		'params'=>array(
			// directory to store uploaded files in that are below document root
			'privateUploadPath' => "/home/www-data/uploads/$wceBusiness/",
		),

		// Application components
		'components' => array(

			// Application Log
			'log' => array(
				'class' => 'CLogRouter',
				'routes' => array(
					// Save log messages on file
					array(
						'class' => 'CFileLogRoute',
						'levels' => 'error, warning, trace, info',
					),
					// Send errors via email to the system admin
					array(
						'class' => 'CEmailLogRoute',
						'levels' => 'error, warning',
						'emails' => 'andrewjblake73@gmail.com',
					),
				),
			),

		),

	),
	
	// This is the Console application configuration. Any writable
	// CConsoleApplication properties can be configured here.
    // Leave array empty if not used.
    // Use value 'inherit' to copy from generated configWeb.
	'configConsole' => array(
	
		// Application components
		'components' => array(
			
			// Application Log
			'log' => 'inherit',

		),

	),

);
