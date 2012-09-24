<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'models'=>$models,
	'action'=>empty($action) ? null : $action, 
	'parent_fk'=>$parent_fk
));

	$form->textFieldRow('name');

	// only allow setting or update of in_charge_id if user has InCharge priveledge
	if(Yii::app()->user->checkAccess('scheduler'))
	{
		StaffController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');
	}

	// if creating
	if($model->isNewRecord)
	{
		ProjectTypeController::listWidgetRow($model, $form, 'project_type_id');
	}
	else
	{
		$form->hiddenField('project_type_id');
	}

	$form->textFieldRow('travel_time_1_way');

	$form->textFieldRow('critical_completion');

	$form->textFieldRow('planned');
	
	// generics
	$this->widget('GenericWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relation_modelToGenericModelType'=>'projectToGenericProjectType',
		'relation_modelToGenericModelTypes'=>'projectToGenericProjectTypes',
		'relation_genericModelType'=>'genericProjectType',
		'relation_category'=>'genericprojectcategory',
		'categoryModelName'=>'Genericprojectcategory',
	));

$this->endWidget();

?>
