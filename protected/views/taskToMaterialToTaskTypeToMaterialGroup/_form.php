<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('material_group_to_material_id');
	$form->hiddenField('material_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('task_type_to_material_group_id');

	$taskTypeToMaterialGroup = TaskTypeToMaterialGroup::model()->findByPk($model->task_type_to_material_group_id);
	
	if($model->isNewRecord)
	{
		// get the default quantity
		$model->quantity = $assemblyToMaterialGroup->quantity;
	}

	MaterialController::listWidgetRow($model, $form, 'material_id', array('data-original-title'=>$taskTypeToMaterialGroup->selection_tooltip), array('scopeMaterialGroup'=>array($model->material_group_id)));

	$form->rangeFieldRow('quantity', $taskTypeToMaterialGroup->minimum, $taskTypeToMaterialGroup->maximum, $taskTypeToMaterialGroup->select, $taskTypeToMaterialGroup->quantity_tooltip);

$this->endWidget();

?>
