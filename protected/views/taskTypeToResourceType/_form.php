<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ResourceTypeController::listWidgetRow($model, $form, 'resource_type_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('hours');

$this->endWidget();

?>