<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('name');

	DutycategoryController::listWidgetRow($model, $form, 'dutycategory_id');

$this->endWidget();

?>
