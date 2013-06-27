<?php

class TaskToAssemblyToAssemblyToAssemblyGroupController extends Controller
{

// TODO: repeated
	protected function createRender($model, $models, $modal_id)
	{
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
	
	public static function getBreadCrumbTrail($lastCrumb = NULL) {
		// alter the breadcrumb trail as necassary for this model - remove the second and third to last items
		$breadCrumbs = parent::getBreadCrumbTrail($lastCrumb);
		array_splice($breadCrumbs, -3, 2);
		return $breadCrumbs;
	}
	
	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model) {
		$modelName = $this->modelName;
	
		// control extra rows of tabs if action is update or create
		if($model && !empty($_GET[$modelName]['task_to_assembly_id']))
		{
			$task_to_assembly_id = $_GET['parent_id'] = $_GET[$modelName]['task_to_assembly_id'];
			$taskToAssemblyController= new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($task_to_assembly_id);
			$taskToAssembly->assertFromParent();
			$taskToAssemblyController->setTabs(NULL);
			$taskToAssemblyController->setActiveTabs(NULL, SubAssembly::getNiceNamePlural());
			static::$tabs = $taskToAssemblyController->tabs;
			
			$tabs=array();
			$lastLabel = $modelName::getNiceName(isset($_GET['id']) ? $_GET['id'] : NULL);
			$this->addTab(
				$lastLabel,
					Yii::app()->controller->modelName,
					Yii::app()->controller->action->id,
					$_GET,
				$tabs,
				TRUE
			);
			static::$tabs = array_merge(static::$tabs, array($tabs));
			
			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail();
		}
		else
		{
			parent::setTabs($model);
		}
	}
}
