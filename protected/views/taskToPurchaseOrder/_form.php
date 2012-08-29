<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	PurchaseOrderController::listWidgetRow($model, $form, 'purchase_order_id');

$this->endWidget();

?>
