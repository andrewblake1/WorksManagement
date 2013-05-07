<?php
/**
 * Main configuration.
 * All properties can be overridden in mode_<mode>.php files
 */
global $wceDatabaseName;

return array(

	// Set YII_DEBUG and YII_TRACE_LEVEL flags
	'yiiDebug' => true,
	'yiiTraceLevel' => 0,

	// Static function Yii::setPathOfAlias()
	'yiiSetPathOfAlias' => array(
		// uncomment the following to define a path alias
		//'local' => 'path/to/local-folder'
	),

	'homeUrl'=>array('client/admin'),
	'defaultController'=>array('client/admin'),

	// This is the main Web application configuration. Any writable
	// CWebApplication properties can be configured here.
	'configWeb' => array(

		'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
		'name' => 'Works Management',
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
			'application.controllers.*',
		),

		'aliases' => array(
			//assuming you extracted the files to the extensions folder
			'xupload' => 'ext.xupload'
		),

		// Application components
		'components' => array(
		
			'user' => array(
				// enable cookie-based authentication
				'allowAutoLogin' => true,
			),

			// Overridden formatting for mysql
			'format'=>array(
				'class'=>'application.components.Formatter',
			),

			// uncomment the following to enable URLs in path-format
			'urlManager'=>array(
				'urlFormat'=>'path',
				'showScriptName'=>false,
				'rules'=>array(
					// removes 'site' & 'index' from urls and add pretty urls to static pages
//					'contact'=>'site/contact',
//					'page/<view:\w+>'=>'site/page',
					'/'=>'site/index',

//					'<controller:\w+>/<id:\d+>'=>'<controller>/view',
					'<controller:\w+>/<action:update>/<id:.+>'=>'<controller>/<action>',
					'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
//					'<controller:\w+>/<action:\w+>/<id:.+>'=>'<controller>/<action>',
					'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

					// add support for modules
//					'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
				),
			),

			// RBAC
			'authManager'=>array(
				'class'=>'CDbAuthManager',
				'connectionID'=>'db',
				'defaultRoles'=>array('default'),
			),
			
			// Database
			'db' => array(
				'connectionString' => "mysql:host=localhost;dbname=$wceDatabaseName",
				'emulatePrepare' => true,
				'username' => '',
				'password' => '',
				'charset' => 'utf8',
			),

			// Database
			'dbReadOnly' => array(
				'class' => 'DbReadOnly',
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
				'responsiveCss' => true,
				'fontAwesomeCss' => true,
			),
			
			// site wide functions
			'functions'=>array(
				'class'=>'application.components.Functions',
			),
			
/*			// widget factory
			'widgetFactory'=>array(
				'class'=>CWidgetFactory,
				'widgets'=>array(
					'CDetailView'=>array(
						'htmlOptions'=>array(
							'class'=>'boxedDetail zebra-striped',
						),
					),
				),
			),*/

		),

		// application-level parameters that can be accessed
		// using Yii::app()->params['paramName']
		'params'=>array(
			// this is used in contact page
			'adminEmail'=>'Hawea.George@westcoastenergy.com.au',
			// delimiter - used when searching and autoseleting as break between fields
			'delimiter'=>array('search'=>'/', 'display'=>' '),
			// maximum number of items to show in list before switching to autotext widget
			'listMax'=>20,
			// show download button or not
			'showDownloadButton'=>true,
			/**
			* @var array provides the hierachy for a breadcrumb trail
			*/
			'trail' => array(
				'Client'=>array(
					'AssemblyToClient',
					'MaterialToClient',
					'ClientContact',
					'Project'=>array( 
						'Day'=>array(
							'Crew'=>array(
								'Task'=>array(
									'Duty',
// TODO: naming orientation is inconsistent here
									'TaskToMaterial',
									'TaskToAssembly',
									'TaskToGenericTaskType',
									'TaskToPurchaseOrder',
									'TaskToResourceType',
								),
							),
						),
						'ProjectToClientContact',
						'ProjectToProjectTypeToAuthItem',
						'ProjectToGenericProjectType',
						'Planning',
					),
					'ProjectType'=>array(
						'ProjectTypeToAuthItem',
						'GenericProjectType',
						'TaskType'=>array(
							'GenericTaskType',
							'TaskTypeToAssembly',
							'TaskTypeToAssemblyGroup',
							'TaskTypeToDutyType',
							'TaskTypeToMaterial',
							'TaskTypeToMaterialGroup',
							'TaskTypeToResourceType',
						),
					),
				),
				'AuthItem'=>array(
					'AuthItemChild',
				),
				'DefaultValue',
				'Dutycategory'=>array(
					'DutyType',
				),
				'GenericType',
				'Genericprojectcategory',
				'Generictaskcategory',
				'Report'=>array(
					'ReportToAuthItem',
					'SubReport',
				),
				'Resourcecategory'=>array(
					'ResourceType'=>array(
						'ResourceTypeToSupplier',
					),
				),
				'Staff'=>array(
					'AuthAssignment',
				),
				'Stage',
				'Store'=>array(
					'Assembly'=>array(
						'SubAssembly',
						'AssemblyToAssemblyGroup',
						'AssemblyToMaterial',
						'AssemblyToMaterialGroup',
						'AssemblyToStandardDrawing',
					),
					'AssemblyGroup'=>array(
						'AssemblyGroupToAssembly',
					),
					'Material',
					'MaterialGroup'=>array(
						'MaterialGroupToMaterial',
					),
					'StandardDrawing',
				),
				'Supplier'=>array(
					'PurchaseOrder',
					'SupplierToSupplierContact'=>array(
						'SupplierContact',
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

		'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
		'name' => 'My Console Application',

		// Preloading 'log' component
		'preload' => array('log'),

		// Autoloading model and component classes
		'import'=>'inherit',

		// Application components home
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