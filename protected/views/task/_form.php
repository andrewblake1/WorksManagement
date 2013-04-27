<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'action'=>empty($action) ? null : $action, 
));

	$form->textFieldRow('name');

	$form->textAreaRow('location');

	// only show when creating
	if($model->isNewRecord)
	{
		TaskTypeController::listWidgetRow($model, $form, 'task_type_id', array(), array(
			'scopeProjectType'=>array($model->crew_id)));
	}
	else
	{
		$form->hiddenField('task_type_id');
		$taskType = $model->taskType;
		$form->rangeFieldRow('quantity', $taskType->minimum, $taskType->maximum, $taskType->select, $taskType->quantity_tooltip);
	}

	// only allow setting or update of in_charge_id if user has correct priveledge
	if(Yii::app()->user->checkAccess('scheduler'))
	{
		StaffController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');
	}


	$form->textFieldRow('planned');

//	$form->textFieldRow('earliest')				;

	$form->checkBoxListInlineRow('preferred', array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'));

	 // generics
	$this->widget('GenericWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relation_modelToGenericModelType'=>'taskToGenericTaskType',
		'relation_modelToGenericModelTypes'=>'taskToGenericTaskTypes',
		'relation_genericModelType'=>'genericTaskType',
		'relation_category'=>'generictaskcategory',
		'categoryModelName'=>'Generictaskcategory',
	));

$this->endWidget();

?>
