<?php

class TaskToMaterialToTaskTemplateToMaterialGroupController extends Controller
{
	protected function createRender($model, $models, $modal_id)
	{
		$taskToMaterial = new TaskToMaterial;
		$taskToMaterial->attributes = $_GET[$this->modelName];
		$taskToMaterial->id = $model->task_to_material_id;
		$taskToMaterial->assertFromParent();

		// set heading
		$this->heading = TaskToMaterial::getNiceName();

		// set breadcrumbs
		$this->breadcrumbs = TaskToMaterialController::getBreadCrumbTrail('Create');
		
		// set tabs
		$this->tabs = $taskToMaterial;
		
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
		$taskToMaterial = TaskToMaterial::model()->findByPk($model->task_to_material_id);
		$taskToMaterial->assertFromParent();
		
		$params = array("TaskToMaterial/admin") + static::getAdminParams('TaskToMaterial');
		
		$this->redirect($params);
	}
	
	
	/**
	 * Get the breadcrumb trail for this controller.
	 * return array bread crumb trail for this controller
	 */
	static function getBreadCrumbTrail($lastCrumb = NULL)
	{
		return TaskToMaterialController::getBreadCrumbTrail('Update');
	}
	
	
/*	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($nextLevel = true) {
		$modelName = $this->modelName;
		$update = FALSE;
			
		parent::setTabs($nextLevel);

		// if create otherwise update
		$_GET['parent_id'] = $task_to_assembly_id = (isset($nextLevel->taskToAssembly) ? $nextLevel->taskToAssembly->id : $nextLevel->taskToMaterial->taskToAssembly->id);
		$taskToAssemblyController= new TaskToAssemblyController(NULL);
		$taskToAssembly = TaskToAssembly::model()->findByPk($task_to_assembly_id);
		$taskToAssembly->assertFromParent();
		$taskToAssemblyController->setTabs(false);
		$taskToAssemblyController->setActiveTabs(NULL, $modelName::getNiceNamePlural());
		$this->_tabs = $taskToAssemblyController->tabs;

		Controller::$nav['update']['TaskToAssembly'] = NULL;
		$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Update');

		$lastLabel = $modelName::getNiceName(isset($_GET['id']) ? $_GET['id'] : NULL);

		$tabs=array();
		$this->addTab($lastLabel, Yii::app()->request->requestUri, $tabs, TRUE);
		$this->_tabs = array_merge($this->_tabs, array($tabs));
		array_pop($this->breadcrumbs);
		$this->breadcrumbs[$modelName::getNiceNamePlural()] = Yii::app()->request->requestUri;
		$this->breadcrumbs[] = $lastLabel;
	}*/
	
}
