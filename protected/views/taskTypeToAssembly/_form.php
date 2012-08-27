<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	AssemblyController::listWidgetRow($model, $form, 'assembly_id');

	if(isset($model->task_type_id))
	{
		$form->hiddenField('task_type_id');
	}
	else
	{
		throw new CHttpException(400, 'No client identified, you must get here from the task types page');
	}

	$form->textFieldRow('quantity');

$this->endWidget();

?>