<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');

	ClientToTaskType::listWidgetRow($model, $form, 'client_to_task_type_id');

	AuthItem::listWidgetRow($model, $form, 'AuthItem_name');

$this->endWidget();

?>
