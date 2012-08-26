<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	MaterialController::listWidgetRow($model, $form, 'material_type_id');

	if(isset($model->task_type_id))
	{
		$form->hiddenField('task_type_id');
	}
	else
	{
		TaskTypeController::listWidgetRow($model, $form, 'task_type_id');
	}

	$form->textFieldRow('quantity');

$this->endWidget();

?>