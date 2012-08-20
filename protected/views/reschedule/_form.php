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

	TaskController::listWidgetRow($model, $form, 'new_task_id');

$this->endWidget();

?>
