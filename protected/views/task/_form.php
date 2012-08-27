<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textAreaRow('description');

	if(isset($model->project_id))
	{
		$form->hiddenField('project_id');
	}
	else
	{
		throw new CHttpException(400, 'No project identified, you must get here from the projects page');
	}

	// only show when creating
	if($model->isNewRecord)
	{
		TaskTypeController::listWidgetRow($model, $form, 'task_type_id');
	}
	else
	{
		$form->hiddenField('task_type_id');
	}

	StaffController::listWidgetRow($model, $form, 'in_charge_id', array(), array(), 'In charge');

	$form->textFieldRow('planned');
	
	$form->textFieldRow('scheduled');
	
	$form->textFieldRow('earliest');
	
	$form->textFieldRow('preferred');

	
	 // generics
	$this->widget('GenericWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relation_modelToGenericModelType'=>'taskToGenericTaskType',
		'relation_modelToGenericModelTypes'=>'taskToGenericTaskTypes',
		'relation_genericModelType'=>'genericTaskType',
	));

$this->endWidget();

?>
