<?php

class DutyStepDependencyController extends Controller
{
		// control extra rows of tabs if action is 
//		if(isset($_GET['parent_duty_step_id']))

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
					'url' => "\$this->controller->createUrl('update', array('id'=>\$data->id, 'action_id' => \$_GET['action_id']) +
						array('duty_step_dependency_ids'=>array_merge(empty(\$_GET['duty_step_dependency_ids']) ? array() : \$_GET['duty_step_dependency_ids'], array(\$data->id))
					))",
				),
				'view' => array(
					'visible' => '
						!Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))
						&& Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => "\$this->controller->createUrl('view', array('id'=>\$data->id, 'action_id' => \$_GET['action_id']) +
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
		$dutyStepDependencyIds = array_slice($_GET['duty_step_dependency_ids'], 0, 1 + array_search($model->child_duty_step_id, $_GET['duty_step_dependency_ids']));
		
		// add tab to  update DutyStepDependency
		$this->addTab(DutyStepDependency::getNiceName(NULL, $model), $this->createUrl('DutyStepDependency/update', array('id' => $model->id, 'action_id' => $_GET['action_id'], 'duty_step_dependency_ids'=>$dutyStepDependencyIds)), $tabs, TRUE);
		
		// add tab to sub assemblies
		$this->addTab(DutyStepDependency::getNiceNamePlural(), $this->createUrl('DutyStepDependency/admin',
			array('action_id' => $_GET['action_id'], 'parent_duty_step_id'=>$model->child_duty_step_id, 'duty_step_dependency_ids'=>$dutyStepDependencyIds)), $tabs);

		return $tabs;
	}

	// override the tabs when viewing assemblies for a particular task
	public function setTabs($model) {
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
