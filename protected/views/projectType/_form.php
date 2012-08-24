<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	// client_id should always be set unless come directly from url so cover this to be safe anyway
	if(isset($model->client_id))
	{
		$form->hiddenField('client_id');
	}
	else
	{
		ClientController::listWidgetRow($model, $form, 'client_id');
	}
	
	ProjectController::listWidgetRow($model, $form, 'template_project_id');

$this->endWidget();

?>