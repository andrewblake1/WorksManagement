<?php

class TaskToMaterialToTaskTemplateToMaterialGroupController extends Controller
{

	protected function updateRedirect($model) {
		$this->redirect(array("TaskToMaterial/admin", 'task_id'=> $model->task_id));
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
