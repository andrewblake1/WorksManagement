<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AssemblyGroupController::dependantListWidgetRow($model, $form, 'assembly_group_id', 'Standard', 'standard_id', array(), array('scopeStandard'=>array($model->standard_id === null ? 0 : $model->standard_id)));

	$form->textFieldRow('comment');

	$form->textFieldRow('quantity');

	$form->textFieldRow('minimum');

	$form->textFieldRow('maximum');

	$form->textAreaRow('select');

	$form->textAreaRow('quantity_tooltip');

	$form->textAreaRow('selection_tooltip');

$this->endWidget();

?>