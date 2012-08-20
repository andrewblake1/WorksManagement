<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->task_id))
	{
		$form->hiddenField('task_id');
	}
	else
	{
		TaskController::listWidgetRow($model, $form, 'task_id');
	}

	ResourceTypeController::listWidgetRow($model, $form, 'resource_type_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('hours');

	$form->textFieldRow('start');

$this->endWidget();

?>
