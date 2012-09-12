<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	ResourceTypeController::listWidgetRow($model, $form, 'resource_type_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('hours');

	$form->textFieldRow('start');

	$form->dropDownListRow('level', Schedule::getLevels());

$this->endWidget();

?>
