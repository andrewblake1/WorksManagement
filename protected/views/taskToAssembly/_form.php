<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	TaskController::listWidgetRow($model, $form, 'task_id');

	AssemblyController::listWidgetRow($model, $form, 'assembly_id');

	$form->textFieldRow($model, 'quantity');

$this->endWidget();

?>
