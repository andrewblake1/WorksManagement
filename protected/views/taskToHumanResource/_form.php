<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	HumanResourceToSupplierController::dependantListWidgetRow($model, $form, 'human_resource_to_supplier_id', 'HumanResource', 'human_resource_id', array(), array('scopeHumanResource'=>array($model->human_resource_id === null ? 0 : $model->human_resource_id)));

	$form->textFieldRow('quantity');

	$form->textFieldRow('estimated_total_quantity');

	$form->textFieldRow('duration');

	$form->timepickerRow('estimated_total_duration');

	$form->timepickerRow('start');

	$form->dropDownListRow('level', Planning::$levels);

//	ModeController::listWidgetRow($model, $form, 'mode_id');

$this->endWidget();

?>