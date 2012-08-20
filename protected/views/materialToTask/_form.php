<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	if(isset($model->task_id))
	{
		$form->hiddenField('task_id');
	}
	else
	{
		TaskController::listWidgetRow($model, $form, 'task_id');
	}

	$form->textFieldRow('quantity');

$this->endWidget();

?>
