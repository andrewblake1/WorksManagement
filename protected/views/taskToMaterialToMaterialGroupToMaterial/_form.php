<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('material_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	if(!$model->isNewRecord)
	{
		$form->hiddenField('material_id');
	}
	MaterialController::listWidgetRow($model, $form, 'material_id', array(), array('scopeMaterialGroup'=>array($model->material_group_id)));

	$form->textFieldRow('quantity');

$this->endWidget();

?>
