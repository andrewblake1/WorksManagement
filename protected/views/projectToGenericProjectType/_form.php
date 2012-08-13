<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	GenericProjectTypeController::listWidgetRow($model, $form, 'generic_project_type_id');

	ProjectController::listWidgetRow($model, $form, 'project_id');

	$form->textFieldRow('generic_id');

$this->endWidget();

?>
