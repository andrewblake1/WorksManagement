<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	TaskController::listWidgetRow($model, $form, 'task_id');

	ProjectToAuthAssignmentToClientToTaskTypeToDutyTypeController::listWidgetRow($model, $form, 'project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id');

	echo $form->textFieldRow($model,'updated',array('class'=>'span5'));

	echo $form->textFieldRow($model,'generic_id',array('class'=>'span5'));

$this->endWidget();

?>
