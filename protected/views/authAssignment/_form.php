<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('itemname');

	StaffController::listWidgetRow($model, $form, 'userid');

	$form->textAreaRow('bizrule');

	$form->textAreaRow('data');

$this->endWidget();

?>
