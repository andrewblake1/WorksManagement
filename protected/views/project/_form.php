<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'models'=>$models));

	$form->textFieldRow('description');

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
