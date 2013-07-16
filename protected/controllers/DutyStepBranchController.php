<?php

class DutyStepBranchController extends Controller
{
	// called within AdminViewWidget
	public function getButtons($model)
	{
		return array(
			'class' => 'WMTbButtonColumn',
			'buttons' => array(
				'delete' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/delete", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
				'update' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => "\$this->controller->createUrl('update', array_merge(array('id'=>\$data->id), \$_GET))",
				),
				'view' => array(
					'visible' => '
						!Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))
						&& Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => "\$this->controller->createUrl('view',  array_merge(array('id'=>\$data->id), Controller::getValidGetParams('$this->modelName')) +
						array('duty_step_dependency_ids'=>array_merge(empty(\$_GET['duty_step_dependency_ids']) ? array() : \$_GET['duty_step_dependency_ids'], array(\$data->id))
					))",
				),
			),
		);
	}

	public function setTabs($model) {

		// in duty step dependency admin view
		$dutyStepDependencyController= new DutyStepDependencyController(NULL);
		$dutyStepDependency = DutyStepDependency::model()->findByPk(array_pop($dutyStepDependencyIds = $_GET['duty_step_dependency_ids']));
		$dutyStepDependency->assertFromParent();
		static::setUpdateId($dutyStepDependency->id, 'DutyStepDependency');
		$dutyStepDependencyController->setTabs($dutyStepDependency);
		$dutyStepDependencyController->setActiveTabs(NULL, DutyStepBranch::getNiceNamePlural(), DutyStepDependency::getNiceNamePlural());
		static::$tabs = $dutyStepDependencyController->tabs;
		
		$this->breadcrumbs = static::getBreadCrumbTrail();
	}
}
