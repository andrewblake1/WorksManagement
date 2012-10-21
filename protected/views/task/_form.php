<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'action'=>empty($action) ? null : $action, 
//	'parent_fk'=>$parent_fk,
));

	$form->textFieldRow('name');

	$form->textAreaRow('location');

	// only show when creating
	if($model->isNewRecord)
	{
		TaskTypeController::listWidgetRow($model, $form, 'task_type_id', array(), array(
			'scopeProjectType'=>array($model->project_id)));
	}
	else
	{
		$form->hiddenField('task_type_id');
	}

	// only allow setting or update of in_charge_id if user has InCharge priveledge
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
