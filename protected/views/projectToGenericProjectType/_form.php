<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	GenericProjectTypeController::listWidgetRow($model, $form, 'generic_project_type_id');

	if(isset($model->project_id))
	{
		$form->hiddenField('project_id');
	}
	else
	{
		ProjectController::listWidgetRow($model, $form, 'project_id');
	}

	$form->textFieldRow('generic_id');

$this->endWidget();

?>
