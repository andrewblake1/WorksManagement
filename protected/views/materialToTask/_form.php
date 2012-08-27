<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	if(isset($model->task_id))
	{
		$form->hiddenField('task_id');
	}
	else
	{
		throw new CHttpException(400, 'No task identified, you must get here from the tasks page');
	}

	$form->textFieldRow('quantity');

$this->endWidget();

?>
