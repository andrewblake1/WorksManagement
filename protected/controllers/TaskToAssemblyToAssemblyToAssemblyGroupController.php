<?php

class TaskToAssemblyToAssemblyToAssemblyGroupController extends Controller
{
	protected function createRender($model, $models, $modal_id)
	{
// TODO: repeated
		$taskToAssembly = new TaskToAssembly;
		$taskToAssembly->attributes = $_GET[$this->modelName];
		$taskToAssembly->id = $model->task_to_assembly_id;
		$taskToAssembly->assertFromParent();

		// set heading
		$this->heading = TaskToAssembly::getNiceName();

		// set breadcrumbs
		$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Create');
		
		// set tabs
		$this->tabs = $taskToAssembly;
		
		echo $this->render('_form',array(
			'model'=>$model,
			'models'=>$models,
		));
	}

	protected function updateRedirect($model) {
		$this->createRedirect($model);
	}

	protected function createRedirect($model)
	{
		// go to admin view
		$taskToAssembly = TaskToAssembly::model()->findByPk($model->task_to_assembly_id);
		$taskToAssembly->assertFromParent();
		
		$params = array("TaskToAssembly/admin") + static::getAdminParams('TaskToAssembly');
		$params['parent_id'] = $taskToAssembly->parent_id;
		
		$this->redirect($params);
	}
	
	
	/**
	 * Get the breadcrumb trail for this controller.
	 * return array bread crumb trail for this controller
	 */
	static function getBreadCrumbTrail($lastCrumb = NULL)
	{
		return TaskToAssemblyController::getBreadCrumbTrail('Update');
	}
	
	
/*	function setUpdateTabs($model) {
		if(!empty($model->task_to_assembly_id))
		{
			// need to trick it here into using task to assembly model instead as this model not in navigation hierachy
			$taskToAssembly = TaskToAssembly::model()->findByPk($model->task_to_assembly_id);
			return parent::setUpdateTabs($taskToAssembly);
		}
		
		return parent::setUpdateTabs($model);
	}*/

/*	public function setCreateTabs($model) {
		$this->setUpdateTabs($model);
	}*/

	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model) {
		$modelName = $this->modelName;
	
		// control extra rows of tabs if action is update or create
		if($model && isset($_GET['TaskToAssemblyToAssemblyToAssemblyGroup']['task_to_assembly_id']))
		{
			$task_to_assembly_id = $_GET['parent_id'] = $_GET['TaskToAssemblyToAssemblyToAssemblyGroup']['task_to_assembly_id'];
			$taskToAssemblyController= new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($task_to_assembly_id);
			$taskToAssembly->assertFromParent();
			$taskToAssemblyController->setTabs(NULL);
			$taskToAssemblyController->setActiveTabs(NULL, SubAssembly::getNiceNamePlural());
			static::$tabs = $taskToAssemblyController->tabs;
			
			$tabs=array();
			$lastLabel = $modelName::getNiceName(isset($_GET['id']) ? $_GET['id'] : NULL);
			$this->addTab($lastLabel, Yii::app()->request->requestUri, $tabs, TRUE);
			static::$tabs = array_merge(static::$tabs, array($tabs));
			
			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail();
		}
		else
		{
			parent::setTabs($model);
		}
	}
}
