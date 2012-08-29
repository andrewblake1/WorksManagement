<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	MaterialController::listWidgetRow($model, $form, 'material_id');

	$form->textFieldRow('quantity');

$this->endWidget();

?>