<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	// get the parent model
	$assembly = Assembly::model()->findByPk(static::getUpdateId('Assembly'));
	AssemblyController::listWidgetRow($model, $form, 'child_assembly_id', array(),
		array('scopeStandard'=>array($assembly->standard_id)));

	$form->textFieldRow('comment');

	$form->textFieldRow('quantity');

	$form->textFieldRow('minimum');

	$form->textFieldRow('maximum');

	$form->textAreaRow('select');

	$form->textAreaRow('quantity_tooltip');

$this->endWidget();

?>