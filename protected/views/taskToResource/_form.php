<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ResourceController::listWidgetRow($model, $form, 'resource_id');

	ResourceToSupplierController::listWidgetRow($model, $form, 'resource_to_supplier_id', array(), array(
		'scopeResource'=>array($model->resource_id)));

	$form->textFieldRow('quantity');

	$form->textFieldRow('duration');

	$form->textFieldRow('start');

	$form->dropDownListRow('level', Planning::$levels);

$this->endWidget();

?>