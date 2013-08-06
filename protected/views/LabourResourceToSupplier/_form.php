<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	SupplierController::listWidgetRow($model, $form, 'supplier_id');

	$form->textFieldRow('unit_price');

$this->endWidget();

?>