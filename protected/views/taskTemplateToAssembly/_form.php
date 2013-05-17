<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));		

	AssemblyController::dependantListWidgetRow($model, $form, 'assembly_id', 'Standard', 'standard_id', array(), array('scopeStandard'=>array($model->standard_id === null ? 0 : $model->standard_id)));

	$form->textFieldRow('quantity');

	$form->textFieldRow('minimum');

	$form->textFieldRow('maximum');

	$form->textAreaRow('quantity_tooltip');

	$form->textAreaRow('select');

$this->endWidget();

?>

