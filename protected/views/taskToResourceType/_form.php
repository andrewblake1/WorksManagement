<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->task_id))
	{
		$form->hiddenField('task_id');
	}
	else
	{
		throw new CHttpException(400, 'No task type identified, you must get here from the task types page');
	}

	ResourceTypeController::listWidgetRow($model, $form, 'resource_type_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('hours');

	$form->textFieldRow('start');

$this->endWidget();

?>
