<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	PurchaseOrderController::listWidgetRow($model, $form, 'purchase_order_id');

$this->endWidget();

?>
