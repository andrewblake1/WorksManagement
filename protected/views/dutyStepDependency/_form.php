<?php
$action = $this->createUrl('create', $_GET);
$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk, 'action'=>$action));

	// hidden field to parent duty step
	if(!empty($_GET['duty_step_dependency_ids']))
	{
		$dutyStepDependency = DutyStepDependency::model()->findByPk(array_pop($_GET['duty_step_dependency_ids']));
		$model->parent_duty_step_id = $dutyStepDependency->child_duty_step_id;
		$form->hiddenField('parent_duty_step_id');
	}

	// get the parent model
	$dutyStep = DutyStep::model()->findByPk(static::getUpdateId('DutyStep'));
	DutyStepController::listWidgetRow($model, $form, 'child_duty_step_id', array(),
		array(/*'scopeStandard'=>array($dutyStep->standard_id)*/));

$this->endWidget();

?>