<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	$form->textFieldRow('url');

	MaterialController::listWidgetRow($model, $form, 'material_id');

	$form->textFieldRow('quantity');

$this->endWidget();

?>