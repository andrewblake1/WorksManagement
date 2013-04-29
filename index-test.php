<?php
/**
 * This is the bootstrap file for test application.
 * This file should be removed when the application is deployed for production.
 */

$yiiDatabaseName = 'worksmanagement_test';
$env = new Environment('DEVELOPMENT'); //override mode
// set character coding to what is used in database. Without this there can be errors in functions like substr which can
// break a muli-byte character midway thru and return garbage. This also means that we should use the mb_ version of string functions
// e.g. mb_substr and mb_strlen instead of substr etc.
mb_internal_encoding("UTF-8");

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
Yii::createWebApplication($config)->run();
