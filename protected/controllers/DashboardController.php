<?php

class DashboardController extends Controller
{
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
	
	public function actionAdmin($exportColumns = array()) {
		$this->redirect($this->createUrl('DashboardDuty/admin'));
	}
	
	public function setTabs($model, &$tabs = NULL) {
		parent::setTabs($model, $tabs);
		
		// set breadcrumbs
		$this->breadcrumbs = static::getBreadCrumbTrail();

		// add another tab layer - can't use ordinary tabs function for this is too many calls requiring a model
		static::$tabs[1] = array(
			array(
				'url' => Yii::app()->createUrl('dashboardDuty/admin'),
				'label' => 'Duties',
				'active' => '1',
			)
		);
		
	}
	
	
}