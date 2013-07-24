<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ResourceController::listWidgetRow($model, $form, 'resource_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('duration');

	$form->textFieldRow('estimated_total_quantity');

	$form->textFieldRow('estimated_total_duration');

	ModeController::listWidgetRow($model, $form, 'mode_id');

$this->endWidget();

?>