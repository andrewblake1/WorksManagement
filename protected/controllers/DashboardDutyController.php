<?php

class DashboardDutyController extends DutyController
{

	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = NULL;

	protected function newButton()
	{
		
	}
	
	public function actionAdmin($exportColumns = array()) {
		
		return parent::actionAdmin($exportColumns);
	}
	/**
	 * @return array action filters
	 */
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array(
			array('allow',
				'actions' => array('admin'),
				'users'=>array('@'),
			),
			array('deny', // deny all users
				'users' => array('*'),
			),
		);
	}
	
	// simulate the tabs in Drawing
	public function setTabs($model) {
		$dashboardController = new DashboardController(NULL);
		$dashboardController->setTabs(NULL);
		static::$tabs = $dashboardController->tabs;
	}
	

}