<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	PlantToSupplierController::dependantListWidgetRow(
		$model,
		$form,
		'plant_to_supplier_id',
		'Plant',
		'plant_id',
		array(),
		array('scopePlant'=>array($model->plant_id))
	);

	$form->dropDownListRow('level', Planning::$levels);

	$form->timepickerRow('durationTemp');

	$form->timepickerRow('estimated_total_duration');

	$form->timepickerRow('start');

	$form->textFieldRow('quantity');

	$form->textFieldRow('estimated_total_quantity');

$this->endWidget();

?>