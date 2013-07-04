<?php

class DashboardTaskController extends TaskController
{

	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
//	protected $_adminViewModel = 'ViewDashboardTask';

	protected function newButton()
	{
		
	}
	
	public function actionUpdate($id) {
		if(isset($_POST['DashboardTask'])) {
			$_POST['Task'] = $_POST['DashboardTask'];
		}
		
		parent::actionUpdate($id);
	}
	
	// redirect to admin - bypass the taskController version as don't want to limit by task
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
		$tabs = array();
		// add tab to  update duty
		$duty = Duty::model()->findByPk($_GET['duty_id']);
		$this->addTab(DashboardDuty::getNiceName(NULL, $duty), 'DashboardDuty', 'update', array('id' => $_GET['duty_id']), static::$tabs[], FALSE, $duty);
		// add tab to view associated tasks
		$this->addTab(DashboardTask::getNiceNamePlural(), 'DashboardTask', 'admin', array(
			'duty_data_id' => $_GET['duty_data_id'],
			'duty_id' => $_GET['duty_id'],
			), static::$tabs[sizeof(static::$tabs) - 1], TRUE);
	}

	public function getButtons($model)
	{
		return array(
			'class' => 'WMTbButtonColumn',
			'buttons' => array(
				'delete' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("Task/delete", array("id"=>$data->primaryKey))',
				),
				'update' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("Task/update", array("id"=>$data->primaryKey))',
				),
				'view' => array(
					'visible' => '
						!Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))
						&& Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => 'Yii::app()->createUrl("Task/view", array("id"=>$data->primaryKey))',
				),
			),
		);
	}
}