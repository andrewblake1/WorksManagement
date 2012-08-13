<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	ClientController::listWidgetRow($model, $form, 'client_id');
	
	TaskController::listWidgetRow($model, $form, 'template_task_id');

$this->endWidget();

?>
