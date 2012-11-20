<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	MaterialController::listWidgetRow($model, $form, 'material_id', array(),
		array('scopeAssembly'=>array($model->assembly_id)));

	$form->textFieldRow('quantity');

	StageController::listWidgetRow($model, $form, 'stage_id');

	$form->textAreaRow('comment');

$this->endWidget();

?>