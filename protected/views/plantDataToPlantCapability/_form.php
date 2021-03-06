<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	PlantCapabilityController::listWidgetRow($model, $form, 'plant_capability_id', array(),
		array(
			'scopePlant'=>array(
				'plantId'=>$model->plantData->plant_id,
			),
			'scopeSupplier'=>array(
				'supplierId'=> ($model->plantToSupplier
					? $model->plantToSupplier->supplier_id
					: null)
			),
		)
	);

	$form->textFieldRow('quantity');

$this->endWidget();

?>