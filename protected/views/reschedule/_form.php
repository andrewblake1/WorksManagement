<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	TaskController::listWidgetRow($model, $form, 'new_task_id');

$this->endWidget();

?>
