<?php

class TaskToAssemblyToAssemblyToAssemblyGroupController extends Controller
{

	protected function updateRedirect($model)
	{
		$params = array("TaskToAssembly/admin") + array(
			'parent_id' => $model->parent_id,
			'task_id' => $model->task_id,
		);

		$this->redirect($params);
	}

	public static function getBreadCrumbTrail($lastCrumb = NULL)
	{
		// alter the breadcrumb trail as necassary for this model - remove the second and third to last items
		$breadCrumbs = parent::getBreadCrumbTrail($lastCrumb);
		array_splice($breadCrumbs, -3, 2);
		return $breadCrumbs;
	}

	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model)
	{
		$modelName = $this->modelName;

		// control extra rows of tabs if action is update or create
		if($model && !empty($_GET[$modelName]['parent_id']))
		{
			$parent_id = $_GET['parent_id'] = $_GET[$modelName]['parent_id'];
			$taskToAssemblyController = new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($parent_id);
			$taskToAssembly->assertFromParent();
			$taskToAssemblyController->setTabs(NULL);
			$taskToAssemblyController->setActiveTabs(NULL, SubAssembly::getNiceNamePlural());
			static::$tabs = $taskToAssemblyController->tabs;

			$tabs = array();
			$lastLabel = $modelName::getNiceName(isset($_GET['id']) ? $_GET['id'] : NULL);
			$this->addTab(
				$lastLabel, Yii::app()->controller->modelName, Yii::app()->controller->action->id, $_GET, $tabs, TRUE
			);
			static::$tabs = array_merge(static::$tabs, array($tabs));

			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail();
		} else
		{
			parent::setTabs($model);
		}
	}

}
