<?php
	$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk, 'action'=>($this->action->id != 'update') ? $this->createUrl('create', $_GET) : null));

	// hidden field to parent duty step
	if(!empty($_GET['duty_step_dependency_ids']))
	{
		$dutyStepDependency = DutyStepDependency::model()->findByPk(array_pop($_GET['duty_step_dependency_ids']));
		$model->duty_step_id = $dutyStepDependency->child_duty_step_id;
		$form->hiddenField('duty_step_id');
	}

	CustomFieldToDutyStepController::listWidgetRow($model, $form, 'custom_field_to_duty_step_id', array(), array('scopeDutyStep'=>array($model->duty_step_id)));

	$form->textFieldRow('compare');

$this->endWidget();

?>