<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	MaterialController::listWidgetRow($model, $form, 'material_id', array(),
		array('scopeAssembly'=>array(Controller::$nav['update']['Assembly'])));

	$form->textFieldRow('quantity');

	StageController::listWidgetRow($model, $form, 'stage_id');

	$form->textAreaRow('comment');

$this->endWidget();

?>