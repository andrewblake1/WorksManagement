<?php

class DutyStepDependencyController extends Controller
{

	public function __construct($id, $module = null) {
		
		ActionController::setTrail();
	
		parent::__construct($id, $module);
	}

	protected function createRedirect($model, $params = array())
	{
		$params = 
			array_merge(array('id'=>$model->id), Controller::getValidGetParams($this->modelName)) +
				array('duty_step_dependency_ids'=>array_merge(empty($_GET['duty_step_dependency_ids']) ? array() : $_GET['duty_step_dependency_ids'], array($model->id)));
		// go to update view
		$this->redirect(array_merge(array('update'), $params));
	}

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
					'url' => "\$this->controller->createUrl('update', array_merge(array('id'=>\$data->id), Controller::getValidGetParams('$this->modelName')) +
						array('duty_step_dependency_ids'=>array_merge(empty(\$_GET['duty_step_dependency_ids']) ? array() : \$_GET['duty_step_dependency_ids'], array(\$data->id))
					))",
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

	public function setChildTabs($model)
	{
		$models = array();

		foreach($_GET['duty_step_dependency_ids'] as $dutyStepDependencyId)
		{
			$models[] = DutyStepDependency::model()->findByPk($dutyStepDependencyId);
		}

		$size = sizeof($models);
		$cntr = 0;
		foreach($models as $model)
		{
			$cntr++;
			if($tabs = $this->getChildTabs($model, $cntr == $size))
			{
				static::$tabs[] = $tabs;
			}
		}

		return static::$tabs;
	}
	public function getChildTabs($model, $last = FALSE)
	{
		$tabs = array();
		
		// need to truncate the array of dependency on per tab level basis
		$dutyStepDependencyIds = array_slice($_GET['duty_step_dependency_ids'], 0, 1 + array_search($model->id, $_GET['duty_step_dependency_ids']));
		
		$getParams = Controller::getValidGetParams($this->modelName);

		// add tab to  update DutyStepDependency
		$this->addTab(
			DutyStepDependency::getNiceName(NULL, $model),
			'DutyStepDependency',
			'update',
			array_merge(array(
				'id' => $model->id,
				'duty_step_dependency_ids'=>$dutyStepDependencyIds
			), $getParams),
			$tabs,
			TRUE
		);
		
		// add tab to sub dependencies
		$this->addTab(
			DutyStepDependency::getNiceNamePlural(),
			'DutyStepDependency',
			'admin',
			array_merge($getParams, array(
				'parent_duty_step_id'=>$model->child_duty_step_id,
				'duty_step_dependency_ids'=>$dutyStepDependencyIds
			)),
			$tabs
		);

		// add tab to branching
		$this->addTab(
			DutyStepBranch::getNiceNamePlural(),
			'DutyStepBranch',
			'admin',
			array_merge($getParams, array(
				'duty_step_dependency_ids'=>$dutyStepDependencyIds,
				'duty_step_dependency_id'=>array_pop($dutyStepDependencyIds),
			)),
			$tabs
		);
 
		return $tabs;
	}

	// override the tabs when viewing assemblies for a particular task
	public function setTabs($model = NULL, &$tabs = NULL) {
		// unset this before calling parent due to multi level issue - top level has no parent
		unset($_GET['parent_duty_step_id']);

		if($model)
		{
			parent::setTabs(NULL);
			$this->setChildTabs($this->loadModel(static::getUpdateId()));
			$this->setActiveTabs(DutyStepDependency::getNiceNamePlural(), FALSE, DutyStepDependency::getNiceNamePlural());
		}
		else
		{
			// if in a sub duty_step
			if(isset($_GET['duty_step_dependency_ids']))
			{
				$dutyStepDependency = DutyStepDependency::model()->findByPk(current($_GET['duty_step_dependency_ids']));
				static::setUpdateId($dutyStepDependency->parent_duty_step_id, 'DutyStep');
				parent::setTabs($model);
				$this->setChildTabs(NULL);
				$this->setActiveTabs(DutyStepDependency::getNiceNamePlural(), DutyStepDependency::getNiceNamePlural(), DutyStepDependency::getNiceNamePlural());
			}
			else
			{
				parent::setTabs($model);
			}
		}

		// set breadcrumbs
		$this->breadcrumbs = self::getBreadCrumbTrail();
	}
	
}

?>