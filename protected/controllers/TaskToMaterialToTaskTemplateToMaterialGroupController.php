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

}
