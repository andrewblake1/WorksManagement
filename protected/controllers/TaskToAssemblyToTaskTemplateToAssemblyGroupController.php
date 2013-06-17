<?php

class TaskToAssemblyToTaskTemplateToAssemblyGroupController extends Controller
{
	
	public static function getBreadCrumbTrail($lastCrumb = NULL) {
		// alter the breadcrumb trail as necassary for this model - remove the second and third to last items
		$breadCrumbs = parent::getBreadCrumbTrail($lastCrumb);
		array_splice($breadCrumbs, -3, 2);
		return $breadCrumbs;
	}
	
	protected function createRender($model, $models, $modal_id)
	{
		// set heading
		$this->heading = TaskToAssembly::getNiceName();

		$taskToAssembly = new TaskToAssembly;
		$taskToAssembly->attributes = $_GET['TaskToAssemblyToTaskTemplateToAssemblyGroup'];
		$taskToAssembly->id = $model->task_to_assembly_id;
		$taskToAssembly->assertFromParent();

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
		
		$params = array("TaskToAssembly/admin");

		$params = array("TaskToAssembly/admin") + static::getAdminParams('TaskToAssembly');
		$params['parent_id'] = $taskToAssembly->parent_id;
		
		$this->redirect($params);
	}
		
}
