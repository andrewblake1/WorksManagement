<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	if(isset($model->task_type_id))
	{
		$form->hiddenField('task_type_id');
	}
	else
	{
		throw new CHttpException(400, 'No task type identified, you must get here from the task types page');
	}

	$form->textFieldRow('quantity');

$this->endWidget();

?>