<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('scheduleName');

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

	StaffController::listWidgetRow($model, $form, 'in_charge_id', array(), array(), 'In charge');

	$form->textFieldRow('planned');

	// NB: show as read only unless Schedule rights
	$form->textFieldRow('scheduled', Yii::app()->user->checkAccess('Schedule') ? array() : array('disabled'=>'disabled'));

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
