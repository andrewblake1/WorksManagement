<?php

class TaskToMaterialToMaterialGroupToMaterialController extends Controller
{
	protected function createRender($model, $models, $modalId)
	{
		// set heading
		$this->heading = TaskToMaterial::getNiceName();

		$taskToMaterial = new TaskToMaterial;
		$taskToMaterial->attributes = $_GET['TaskToMaterialToMaterialGroupToMaterial'];
		$taskToMaterial->assertFromParent();

		// set breadcrumbs
		$this->breadcrumbs = TaskToMaterialController::getBreadCrumbTrail('Create');
		
		// set tabs
		$this->setUpdateTabs($taskToMaterial);
		
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
		
		$params = array("TaskToMaterial/admin");

		if (isset(Controller::$nav['admin']['TaskToMaterial'])) {
			$params += Controller::$nav['admin']['TaskToMaterial'];
		}

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
	
	
	function setUpdateTabs($model) {
		if(!empty($model->task_to_material_id))
		{
			// need to trick it here into using task to material model instead as this model not in navigation hierachy
			$taskToMaterial = TaskToMaterial::model()->findByPk($model->task_to_material_id);
			return parent::setUpdateTabs($taskToMaterial);
		}
		
		return parent::setUpdateTabs($model);
	}
	
}
