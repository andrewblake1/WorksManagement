<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	ClientController::listWidgetRow($model, $form, 'client_id');

	TaskTypeController::listWidgetRow($model, $form, 'task_type_id');

$this->endWidget();

?>
