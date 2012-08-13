<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	ResourceTypeController::listWidgetRow($model, $form, 'resource_type_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('hours');

	$form->textFieldRow('start');

$this->endWidget();

?>
