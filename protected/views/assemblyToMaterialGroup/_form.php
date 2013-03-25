<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

// TODO: should set store id in controller
	$assembly = Assembly::model()->findByPk($model->assembly_id);
	MaterialGroupController::listWidgetRow($model, $form, 'material_group_id', array(),
		array('scopeStore'=>array($assembly->store_id)));

	$form->textFieldRow('quantity');

	StageController::listWidgetRow($model, $form, 'stage_id');
 
$this->endWidget();

?>