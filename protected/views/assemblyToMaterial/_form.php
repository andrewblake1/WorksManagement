<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set standard id in controller
	$assembly = Assembly::model()->findByPk($model->assembly_id);
	MaterialController::listWidgetRow($model, $form, 'material_id', array(),
		array('scopeStandard'=>array($assembly->standard_id)));

	StageController::listWidgetRow($model, $form, 'stage_id');

	DrawingController::listWidgetRow($model, $form, 'detail_drawing_id', array(), array('scopeStandard'=>array($model->standard_id)), 'Drawing');

	$form->textFieldRow('quantity');

	$form->textFieldRow('minimum');

	$form->textFieldRow('maximum');

	$form->textAreaRow('quantity_tooltip');

	$form->textAreaRow('select');

$this->endWidget();

?>