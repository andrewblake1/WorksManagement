<?php

class TaskToAssemblyToTaskTypeToAssemblyGroupController extends Controller
{
	protected function createRender($model, $models, $modalId)
	{
		// set heading
		$this->heading = TaskToAssembly::getNiceName();

		$taskToAssembly = new TaskToAssembly;
		$taskToAssembly->attributes = $_GET['TaskToAssemblyToTaskTypeToAssemblyGroup'];
		$taskToAssembly->assertFromParent();

		// set breadcrumbs
		$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Create');
		
		// set tabs
		$this->setUpdateTabs($taskToAssembly);
		
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
		
		$params = array("TaskToAssembly/admin");

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
	
	// override the tabs when viewing materials for a particular task - make match task_to_assembly view
	public function setUpdateTabs($model) {
		$modelName = $this->modelName;
	
		// control extra rows of tabs if action is update or create
		if(isset($_GET['TaskToAssemblyToTaskTypeToAssemblyGroup']['task_to_assembly_id']))
		{
			$task_to_assembly_id = $_GET['parent_id'] = $_GET['TaskToAssemblyToTaskTypeToAssemblyGroup']['task_to_assembly_id'];
			$taskToAssemblyController= new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($task_to_assembly_id);
			$taskToAssembly->assertFromParent();
			$taskToAssemblyController->setTabs(false);
			$taskToAssemblyController->setActiveTabs(NULL, SubAssembly::getNiceNamePlural());
			$this->_tabs = $taskToAssemblyController->tabs;
			
			$tabs=array();
			$lastLabel = $modelName::getNiceName(isset($_GET['id']) ? $_GET['id'] : NULL);
			$this->addTab($lastLabel, Yii::app()->request->requestUri, $tabs, TRUE);
			$this->_tabs = array_merge($this->_tabs, array($tabs));
			
			// set breadcrumbs
			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Update');
			array_pop($this->breadcrumbs);
			$this->breadcrumbs[] = $lastLabel;
		}
		else
		{
			parent::setTabs($model);
		}
	}

}
