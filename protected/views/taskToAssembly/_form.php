<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->task_id))
	{
		$form->hiddenField('task_id');
	}
	else
	{
		throw new CHttpException(400, 'No task identified, you must get here from the tasks page');
	}

	AssemblyController::listWidgetRow($model, $form, 'assembly_id');

	$form->textFieldRow('quantity');

$this->endWidget();

?>
