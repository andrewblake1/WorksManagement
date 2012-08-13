<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	SupplierController::listWidgetRow($model, $form, 'supplier_id');

	$form->textFieldRow('number');

$this->endWidget();

?>
