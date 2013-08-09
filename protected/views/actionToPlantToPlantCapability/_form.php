<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	PlantCapabilityController::listWidgetRow($model, $form, 'plant_capability_id', array(),
		array(
			'scopePlant'=>array(
				'plantId'=>$model->actionToPlant->plant_id,
			),
			'scopeSupplier'=>array(
				'supplierId'=> ($model->actionToPlant->plantToSupplier
					? $model->actionToPlant->plantToSupplier->supplier_id
					: null)
			),
		)
	);

	$form->textFieldRow('quantity');

$this->endWidget();

?>