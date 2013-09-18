<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	// hidden field to parent assembly
	if($model->isNewRecord && !empty($_GET['sub_assembly_ids']))
	{
		$subAssembly = SubAssembly::model()->findByPk(array_pop($_GET['sub_assembly_ids']));
		$model->parent_assembly_id = $subAssembly->child_assembly_id;
		$form->hiddenField('parent_assembly_id');
	}

	// get the parent model
	$assembly = Assembly::model()->findByPk(static::getUpdateId('Assembly'));
	AssemblyController::listWidgetRow($model, $form, 'child_assembly_id', array(),
		array('scopeStandard'=>array($assembly->standard_id)));

	DrawingController::listWidgetRow($model, $form, 'detail_drawing_id', array(), array('scopeStandard'=>array($assembly->standard_id)), 'Drawing');

	$form->textFieldRow('comment');

	$form->textFieldRow('quantity');

	$form->textFieldRow('minimum');

	$form->textFieldRow('maximum');

	$form->textAreaRow('quantity_tooltip');

	$form->textAreaRow('select');

$this->endWidget();

?>