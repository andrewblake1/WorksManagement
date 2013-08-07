<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	PlantCapabilityController::listWidgetRow($model, $form, 'plant_capability_id', array(), array('scopePlant'=>array('plantId'=>$model->plantToSupplier->plant_id)));

	$form->textFieldRow('unit_price');

$this->endWidget();

?>