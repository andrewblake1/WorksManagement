<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	if(isset($model->project_type_id))
	{
		$form->hiddenField('project_type_id');
	}
	else
	{
		throw new CHttpException(400, 'No task type identified, you must get here from the task types page');
	}
/*
 * provisional

	TaskController::listWidgetRow($model, $form, 'template_task_id', array(), array(), 'Template task');
 */
	
$this->endWidget();

?>
