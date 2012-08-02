<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	TaskController::listWidgetRow($model, $form, 'old_task_id');

	TaskController::listWidgetRow($model, $form, 'new_task_id');

$this->endWidget();

?>
