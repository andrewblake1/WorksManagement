<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set store id in controller
	$assembly = Assembly::model()->findByPk($model->assembly_id);
	MaterialGroupController::listWidgetRow($model, $form, 'material_group_id', array(),
		array('scopeStore'=>array($assembly->store_id)));

	StageController::listWidgetRow($model, $form, 'stage_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('minimum');

	$form->textFieldRow('maximum');

	$form->textAreaRow('select');

	$form->textAreaRow('quantity_tooltip');

	$form->textAreaRow('selection_tooltip');

$this->endWidget();

?>