<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'models'=>$models));

	$form->textFieldRow('description');
	
	// only show the project type drop down when creating
	if($model->isNewRecord)
		ProjectTypeController::listWidgetRow($model, $form, 'project_type_id');

	$form->textFieldRow('travel_time_1_way');

	$form->textFieldRow('critical_completion');

	$form->textFieldRow('planned');
	
	// generics
	$this->widget('GenericWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'toGenericTypeRelation'=>'projectToGenericProjectTypes',
		'genericTypeRelation'=>'genericProjectType',
	));

$this->endWidget();

?>
