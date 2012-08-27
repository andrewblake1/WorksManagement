<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	ResourceTypeController::listWidgetRow($model, $form, 'resource_type_id');

	if(isset($model->task_type_id))
	{
		$form->hiddenField('task_type_id');
	}
	else
	{
		throw new CHttpException(400, 'No task type identified, you must get here from the task types page');
	}

	$form->textFieldRow('quantity');

	$form->textFieldRow('hours');

$this->endWidget();

?>