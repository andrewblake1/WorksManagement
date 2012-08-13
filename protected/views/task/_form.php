<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textAreaRow('description');

	$form->textFieldRow('day');

	PurchaseOrderController::listWidgetRow($model, $form, 'purchase_order_id');

	CrewController::listWidgetRow($model, $form, 'crew_id');

	ProjectController::listWidgetRow($model, $form, 'project_id');

	TaskTypeController::listWidgetRow($model, $form, 'task_type_id');

$this->endWidget();

?>
