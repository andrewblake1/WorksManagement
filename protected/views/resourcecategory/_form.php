<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('root');

	$form->textFieldRow('lft');

	$form->textFieldRow('rgt');

	$form->textFieldRow('level');

	DutyCategoryController::listWidgetRow($model, $form, 'duty_category_id');

	$form->textFieldRow('description');

$this->endWidget();

?>
