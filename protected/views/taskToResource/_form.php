<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ResourceToSupplierController::dependantListWidgetRow($model, $form, 'resource_to_supplier_id', 'Resource', 'resource_id', array(), array('scopeResource'=>array($model->resource_id === null ? 0 : $model->resource_id)));

	$form->textFieldRow('quantity');

	$form->textFieldRow('estimated_total_quantity');

	$form->textFieldRow('duration');

	$form->timepickerRow('estimated_total_duration');

	$form->timepickerRow('start');

	$form->dropDownListRow('level', Planning::$levels);

	ModeController::listWidgetRow($model, $form, 'mode_id');

$this->endWidget();

?>