<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	PlantCapabilityController::listWidgetRow($model, $form, 'plant_capability_id', array(),
		array(
			'scopePlant'=>array(
				'plantId'=>$model->actionToPlantToPlant->plant_id,
			),
			'scopeSupplier'=>array(
				'supplierId'=> ($model->actionToPlantToPlant->plantToSupplier
					? $model->actionToPlantToPlant->plantToSupplier->supplier_id
					: null)
			),
		)
	);

	$form->textFieldRow('quantity');

$this->endWidget();

?>