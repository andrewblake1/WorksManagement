<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	// get the parent model
	$assembly = Assembly::model()->findByPk(static::getUpdateId('Assembly'));
	AssemblyController::listWidgetRow($model, $form, 'child_assembly_id', array(),
		array('scopeStore'=>array($assembly->store_id)));

	$form->textFieldRow('quantity');

	$form->textFieldRow('minimum');

	$form->textFieldRow('maximum');

	$form->textAreaRow('quantity_tooltip');

	$form->textAreaRow('select');

$this->endWidget();

?>