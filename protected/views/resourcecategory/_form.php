<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('root');

	$form->textFieldRow('lft');

	$form->textFieldRow('rgt');

	$form->textFieldRow('level');

	DutycategoryController::listWidgetRow($model, $form, 'dutycategory_id');

	$form->textFieldRow('description');

$this->endWidget();

?>
