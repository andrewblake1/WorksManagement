<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	AssemblyController::listWidgetRow($model, $form, 'assembly_id');

	$form->textFieldRow('quantity');

$this->endWidget();

?>
