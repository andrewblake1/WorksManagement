<?php
/**
 * Main configuration.
 * All properties can be overridden in mode_<mode>.php files
 */
global $wceDatabaseName;

return array(

	// Set YII_DEBUG and YII_TRACE_LEVEL flags
	'yiiDebug' => true,
	'yiiTraceLevel' => 3,

	// Static function Yii::setPathOfAlias()
	'yiiSetPathOfAlias' => array(
		// uncomment the following to define a path alias
		//'local' => 'path/to/local-folder'
	),

	'homeUrl'=>array('dashboard/admin'),
	'defaultController'=>array('dashboard/admin'),

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
			'xupload' => 'ext.xupload'
		),

		// Application components
		'components' => array(

			'clientScript'=>array(
				'class'=>'CClientScript',
				'packages'=>array(
					'jquery.ui'=>array(
						'baseUrl'=>'//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/',
							'js'=>array('jquery-ui.min.js'),
					),
				),
			),	


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
				'class'=>'DbAuthManager',
				'connectionID'=>'db',
				'defaultRoles'=>array('default'),
			),

			// Database
			'db' => array(
				'class'=>'DbConnection',
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
			// table prefix
			'tablePrefix'=>'tbl_',
			// temporary table prefix
			'tempTablePrefix'=>'tmp_',
			/**
			* @var array provides the hierachy for a breadcrumb trail
			*/
			'trail' => array(
				'Dashboard'=>array(
					'DashboardDuty'=>array(
						'DashboardTask',
					),
				),
				'Action'=>array(
					'DutyStepDependency'=>array(
						'DutyStepBranch'
						),
					'DutyStep'=>array(
						'CustomFieldDutyStepCategory'=>array(
							'DutyStepToCustomField',
						),
						'DutyStepToMode',
					),
					'ActionToLabourResource'=>array(
						'ActionToLabourResourceBranch'
					),
					'ActionToPlant'=>array(
						'ActionToPlantBranch',
						'ActionToPlantToPlantCapability',
					),
				),
				'Client'=>array(
					'Action'=>array(
						'DutyStepDependency'=>array(
							'DutyStepBranch'
							),
						'DutyStep'=>array(
							'CustomFieldDutyStepCategory'=>array(
								'DutyStepToCustomField',
							),
							'DutyStepToMode',
						),
						'ActionToLabourResource'=>array(
							'ActionToLabourResourceBranch'
						),
						'ActionToPlant'=>array(
							'ActionToPlantBranch',
							'ActionToPlantToPlantCapability',
						),
					),
					'ClientToAssembly',
					'ClientToMaterial',
					'ClientContact',
					'Project'=>array( 
						'Day'=>array(
							'Crew'=>array(
								'Task'=>array(
									'TaskToAction'=>array(
										'Duty',
									),
									'TaskToAssembly'=>array(
										'TaskToAssemblyToAssemblyToAssemblyGroup',
										'TaskToAssemblyToTaskTemplateToAssemblyGroup',
									),
									'TaskToLabourResource'=>array(
										'MutuallyExclusiveRole',
									),
									'TaskToPlant'=>array(
										'PlantDataToPlantCapability',
									),
									'TaskToMaterial',
								),
							),
						),
						'ProjectToClientContact',
						'ProjectToAuthItem'=>array(
							'ProjectToAuthItemToAuthAssignment',
						),
						'Planning',
					),
					'ProjectTemplate'=>array(
						'Action'=>array(
							'DutyStepDependency'=>array(
								'DutyStepBranch'
								),
							'DutyStep'=>array(
								'CustomFieldDutyStepCategory'=>array(
									'DutyStepToCustomField',
								),
								'DutyStepToMode',
							),
							'ActionToLabourResource'=>array(
								'ActionToLabourResourceBranch'
							),
							'ActionToPlant'=>array(
								'ActionToPlantBranch',
								'ActionToPlantToPlantCapability',
							),
						),
						'CustomFieldProjectCategory'=>array(
							'ProjectTemplateToCustomField',
						),
						'ProjectTemplateToAuthItem',
						'TaskTemplate'=>array(
							'CustomFieldTaskCategory'=>array(
								'TaskTemplateToCustomField',
							),
							'TaskTemplateToAction'=>array(
								'TaskTemplateToActionToLabourResource',
								'TaskTemplateToActionToPlant',
							),
							'TaskTemplateToAssembly',
							'TaskTemplateToAssemblyGroup',
							'TaskTemplateToLabourResource'=>array(
								'TaskTemplateToMutuallyExclusiveRole',
							),
							'TaskTemplateToPlant'=>array(
								'TaskTemplateToPlantCapability',
							),
							'TaskTemplateToMaterial',
							'TaskTemplateToMaterialGroup',
						),
					),
					'ProjectType',
				),
				'Contact',
				'CustomField',
				'DefaultValue',
				'Mode',
				'Report'=>array(
					'ReportToAuthItem',
					'SubReport',
				),
				'LabourResource'=>array(
					'LabourResourceToSupplier',
				),
				'Plant'=>array(
					'PlantCapability',
					'PlantToSupplier'=>array(
						'PlantToSupplierToPlantCapability'
					),
				),
				'AuthItem'=>array(
					'AuthItemChild',
				),
				'Stage',
				'Standard'=>array(
					'Assembly'=>array(
						'SubAssembly',
						'AssemblyToAssemblyGroup',
						'AssemblyToMaterial',
						'AssemblyToMaterialGroup',
					),
					'AssemblyGroup'=>array(
						'AssemblyGroupToAssembly',
					),
					'Material',
					'MaterialGroup'=>array(
						'MaterialGroupToMaterial',
					),
					'Drawing'=>array(
						'DrawingToAssembly',
						'DrawingToMaterial',
					),
				),
				'Supplier'=>array(
					'SupplierContact',
				),
				'User'=>array(
					'AuthAssignment',
				),
			),
		),

	),

);