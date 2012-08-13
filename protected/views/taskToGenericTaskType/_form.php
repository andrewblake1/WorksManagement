<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	TaskController::listWidgetRow($model, $form, 'task_id');

	GenericTaskTypeController::listWidgetRow($model, $form, 'generic_task_type_id');

	$form->textFieldRow('generic_id');

$this->endWidget();

?>
