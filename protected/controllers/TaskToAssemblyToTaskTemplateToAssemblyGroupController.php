<?php

class TaskToAssemblyToTaskTemplateToAssemblyGroupController extends Controller
{
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
