<?php
//phpinfo();exit;
// set environment
//date_default_timezone_set('Pacific/Auckland');

require_once(dirname(__FILE__) . '/protected/extensions/yii-environment/Environment.php');

// set the enviroment based on server name
//$env = new Environment();
switch($_SERVER['SERVER_NAME'])
{
	case 'localhost' :
	case 'test.wcewm.co.nz' :
		$env = new Environment('DEVELOPMENT'); //override mode
		break;
	case 'wcewm.co.nz' :
		$env = new Environment('PRODUCTION'); //override mode
		break;
	default :
		throw new Exception("unknown server {$_SERVER['SERVER_NAME']}"); 
}
//$env = new Environment('TEST'); //override mode

// set debug and trace level
defined('YII_DEBUG') or define('YII_DEBUG', $env->yiiDebug);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', $env->yiiTraceLevel);

// run Yii app
//$env->showDebug(); // show produced environment configuration
require_once($env->yiiPath);
$env->runYiiStatics(); // like Yii::setPathOfAlias()

Yii::createWebApplication($env->configWeb)->run();


/*

// change the following paths if necessary
$yii=dirname(__FILE__).'/../../yii-1.1.10.r3566/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
*/
?>