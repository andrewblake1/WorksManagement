<?php

class TaskToAssemblyToAssemblyGroupToAssemblyController extends Controller
{
	protected function createRender($model, $models, $modalId)
	{
		// set heading
		$this->heading = TaskToAssembly::getNiceName();

		$taskToAssembly = new TaskToAssembly;
		$taskToAssembly->attributes = $_GET['TaskToAssemblyToAssemblyGroupToAssembly'];
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

	protected function createSave($model, &$models=array())
	{
	
		$taskToAssembly = new TaskToAssembly;
		$taskToAssembly->attributes = $_POST['TaskToAssemblyToAssemblyGroupToAssembly'];
		// filler - unused in this context but necassary in Assembly model
		$taskToAssembly->store_id = 0;

		if($saved = parent::createSave($taskToAssembly, $models))
		{
			$model->task_to_assembly_id = $taskToAssembly->id;
			$saved &= parent::createSave($model, $models);
		}

		return $saved;
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

		if (isset(Controller::$nav['admin']['TaskToAssembly'])) {
			$params += Controller::$nav['admin']['TaskToAssembly'];
		}

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
	
	
	function setUpdateTabs($model) {
		if(!empty($model->task_to_assembly_id))
		{
			// need to trick it here into using task to assembly model instead as this model not in navigation hierachy
			$taskToAssembly = TaskToAssembly::model()->findByPk($model->task_to_assembly_id);
			return parent::setUpdateTabs($taskToAssembly);
		}
		
		return parent::setUpdateTabs($model);
	}

}
