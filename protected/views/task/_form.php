<?php

$form=$this->beginWidget('WMBootActiveForm', array('model'=>$model));

	echo $form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8'));

	echo $form->textFieldRow($model,'day',array('class'=>'span5','maxlength'=>10));

	PurchaseOrderController::listWidgetRow($model, $form, 'purchase_order_id');

	CrewController::listWidgetRow($model, $form, 'crew_id');

	ProjectController::listWidgetRow($model, $form, 'project_id');

	ClientToTaskTypeController::listWidgetRow($model, $form, 'client_to_task_type_id');

$this->endWidget();

?>
