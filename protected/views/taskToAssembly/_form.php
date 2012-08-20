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

	AssemblyController::listWidgetRow($model, $form, 'assembly_id');

	$form->textFieldRow($model, 'quantity');

$this->endWidget();

?>
