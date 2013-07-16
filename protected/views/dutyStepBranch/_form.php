<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$dutyStepDependency = DutyStepDependency::model()->findByPk($model->duty_step_dependency_id);
	$model->duty_step_id = $dutyStepDependency->child_duty_step_id;
	$form->hiddenField('duty_step_id');

	CustomFieldToDutyStepController::listWidgetRow($model, $form, 'custom_field_to_duty_step_id', array(), array('scopeDutyStep'=>array($model->duty_step_id)));

	$form->textFieldRow('compare');

$this->endWidget();

?>