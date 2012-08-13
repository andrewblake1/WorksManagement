<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	ClientController::listWidgetRow($model, $form, 'client_id');
	
	ProjectController::listWidgetRow($model, $form, 'template_project_id');

$this->endWidget();

?>
