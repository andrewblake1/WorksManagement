<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	ProjectToAuthAssignmentController::listWidgetRow($model, $form, 'project_to_AuthAssignment_id');

	ClientToTaskTypeToDutyTypeController::listWidgetRow($model, $form, 'client_to_task_type_to_duty_type_id');

$this->endWidget();

?>
