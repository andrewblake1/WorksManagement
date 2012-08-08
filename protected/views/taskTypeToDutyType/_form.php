<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');

	TaskTypeController::listWidgetRow($model, $form, 'task_type_id');

	AuthItemController::listWidgetRow($model, $form, 'AuthItem_name');

$this->endWidget();

?>
