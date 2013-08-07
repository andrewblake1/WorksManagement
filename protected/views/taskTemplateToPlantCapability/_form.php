<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	PlantToSupplierToPlantCapabiltyController::dependantListWidgetRow(
		$model,
		$form,
		'plant_to_supplier_id',
		'PlantCapability',
		'plant_capability_id',
		array(),
		array('scopePlantToSupplier'=>array('plantToSupplierId'=>$model->plant_to_supplier_id))
	);

	$form->textFieldRow('quantity');

$this->endWidget();

?>