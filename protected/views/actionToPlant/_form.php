<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	PlantToSupplierController::dependantListWidgetRow(
		$model,
		$form,
		'plant_to_supplier_id',
		'Plant',
		'plant_id',
		array(),
		array('scopePlant'=>array('plantId'=>$model->plant_id))
	);

	ModeController::listWidgetRow($model, $form, 'mode_id');

	$form->dropDownListRow('level', Planning::$levels);

	$form->textFieldRow('quantity');

$this->endWidget();

?>