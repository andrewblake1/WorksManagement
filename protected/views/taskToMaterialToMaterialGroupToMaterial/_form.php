<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('material_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('assembly_to_material_group_id');
$t=$model->attributes;
	if(!$model->isNewRecord)
	{
		$form->hiddenField('material_id');
		$taskToMaterialId = TaskToMaterial::model()->findByPk($model->task_to_material_id);
		$model->quantity = $taskToMaterialId->quantity;
	}
	else
	{
		// get the default quantity
		$assemblyToMaterialGroup = AssemblyToMaterialGroup::model()->findByPk($model->assembly_to_material_group_id);
		$model->quantity = $assemblyToMaterialGroup->quantity;
	}
	MaterialController::listWidgetRow($model, $form, 'material_id', array(), array('scopeMaterialGroup'=>array($model->material_group_id)));

	$form->textFieldRow('quantity');

$this->endWidget();

?>
