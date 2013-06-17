<?php

class DashboardDutyController extends DutyController
{

	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = 'ViewDashboardDuty';

	protected function newButton()
	{
		
	}
	
	public function actionUpdate($id) {
		if(isset($_POST['DashboardDuty'])) {
			$_POST['Duty'] = $_POST['DashboardDuty'];
		}
		
		parent::actionUpdate($id);
	}
	
	// redirect to admin - bypass the dutyController version as don't want to limit by task
	protected function adminRedirect($model, $sortByNewest = false) {
		static::staticAdminRedirect($model, $sortByNewest);
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
				'actions' => array('admin', 'view', 'update'),
				'users'=>array('@'),
			),
			array('deny', // deny all users
				'users' => array('*'),
			),
		);
	}
	
	public function setTabs($model) {
		$dashboardController = new DashboardController(NULL);
		$dashboardController->setTabs(NULL);
		static::$tabs = $dashboardController->tabs;
		// if update of view
		if(!empty($model))
		{
			$tabs = array();
			// add tab to  update SubAssembly
			$this->addTab(DashboardDuty::getNiceName(NULL, $model), 'DashboardDuty', Yii::app()->controller->action->id, array('id' => $model->id), static::$tabs[], TRUE);
		}
		
	}
	

}