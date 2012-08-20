<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->project_to_AuthAssignment_id))
	{
		$form->hiddenField('project_to_AuthAssignment_id');
	}
	else
	{
		ProjectToAuthAssignmentController::listWidgetRow($model, $form, 'project_to_AuthAssignment_id');
	}

	TaskTypeToDutyTypeController::listWidgetRow($model, $form, 'task_type_to_duty_type_id');

$this->endWidget();

?>
