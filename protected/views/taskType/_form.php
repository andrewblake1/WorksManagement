<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	if(isset($model->project_type_id))
	{
		$form->hiddenField('project_type_id');
	}
	else
	{
		ProjectTypeController::listWidgetRow($model, $form, 'project_type_id');
	}
	
	TaskController::listWidgetRow($model, $form, 'template_task_id');

$this->endWidget();

?>
