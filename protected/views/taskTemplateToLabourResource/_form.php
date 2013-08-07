<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	LabourResourceToSupplierController::dependantListWidgetRow(
		$model,
		$form,
		'labour_resource_to_supplier_id',
		'LabourResource',
		'labour_resource_id',
		array(),
		array('scopeLabourResource'=>array('labourResourceId'=>$model->labour_resource_id))
	);

	$form->textFieldRow('quantity');

	$form->textFieldRow('duration');

	ModeController::listWidgetRow($model, $form, 'mode_id');

	$form->dropDownListRow('level', Planning::$levels);

$this->endWidget();

?>