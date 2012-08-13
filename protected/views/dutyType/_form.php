<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	$form->textFieldRow('lead_in_days');

	DutyCategoryController::listWidgetRow($model, $form, 'duty_category_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
