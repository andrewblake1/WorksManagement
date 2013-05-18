<?php
// logging variables to firebug
// http://www.yiiframework.com/wiki/114/how-to-log-and-debug-variables-using-cweblogroute/
function fb($what){
  echo Yii::trace(CVarDumper::dumpAsString($what),'vardump');
}

require_once(dirname(__FILE__) . '/protected/extensions/yii-environment/Environment.php');

// set the enviroment based on server name
//$env = new Environment();
switch($_SERVER['SERVER_NAME'])
{
	case 'localhost' :
		$wceBusiness = 'melbourne';
		$wceEnvironment = 'DEVELOPMENT';
		break;
	case 'dev.melbourne.wcewm.co.nz' :
		$wceBusiness = 'melbourne';
		$wceEnvironment = 'DEVELOPMENT';
		break;
	case 'dev.perth.wcewm.co.nz' :
		$wceBusiness = 'perth';
		$wceEnvironment = 'DEVELOPMENT';
		break;
	case 'melbourne.wcewm.co.nz' :
		$wceBusiness = 'melbourne';
		$wceEnvironment = 'PRODUCTION';
		break;
	case 'perth.wcewm.co.nz' :
		$wceBusiness = 'perth';
		$wceEnvironment = 'PRODUCTION';
		break;
	default :
		throw new Exception("unknown server {$_SERVER['SERVER_NAME']}"); 
}

$wceDatabaseName = "worksmanagement_{$wceBusiness}";
if('DEVELOPMENT' == $wceEnvironment)
{
	$wceDatabaseName .= '_dev';
}

$env = new Environment($wceEnvironment); //override mode


// set character coding to what is used in database. Without this there can be errors in functions like substr which can
// break a muli-byte character midway thru and return garbage. This also means that we should use the mb_ version of string functions
// e.g. mb_substr and mb_strlen instead of substr etc.
mb_internal_encoding("UTF-8");

// set debug and trace level
defined('YII_DEBUG') or define('YII_DEBUG', $env->yiiDebug);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', $env->yiiTraceLevel);

// run Yii app
//$env->showDebug(); // show produced environment configuration
require_once($env->yiiPath);
$env->runYiiStatics(); // like Yii::setPathOfAlias()

// This shifted here from main view file due to ajax requests returning script files to automatically bind new
// elements - with ajax request may not get to main view include
Yii::app()->clientScript->scriptMap=array(
	// TODO: remove this once the bug is fixed in future release of yiibooster
	/* NB: currently version 1.9 of jqueryUI introduces tootip which conflicts with bootstrap.
		* new version of bootstrap or yiibooster will resolve it - several people working on it
		* There is a jquery-ui solution evidently using $.widget.bridge from https://github.com/twitter/bootstrap/issues/6303
		*/
	'jquery-ui.min.js'=>'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js',
);

Yii::createWebApplication($env->configWeb)->run();

?>