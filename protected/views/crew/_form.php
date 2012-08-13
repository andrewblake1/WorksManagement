<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('preferred_date');

	$form->textFieldRow('earliest_date');

	$form->textFieldRow('date_scheduled');

	StaffController::listWidgetRow($model, $form, 'in_charge_id');

$this->endWidget();

?>
