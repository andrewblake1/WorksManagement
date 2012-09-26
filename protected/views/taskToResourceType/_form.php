<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ResourceTypeController::listWidgetRow($model, $form, 'resource_type_id');

	ResourceTypeToSupplierController::listWidgetRow($model, $form, 'resource_type_to_supplier_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('hours');

	$form->textFieldRow('start');

	$form->dropDownListRow('level', Planning::$levels);

$this->endWidget();

?>