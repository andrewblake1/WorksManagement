<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	AuthAssignmentController::listWidgetRow($model, $form, 'project_id');

	AuthAssignmentController::listWidgetRow($model, $form, 'AuthAssignment_id');

$this->endWidget();

?>
