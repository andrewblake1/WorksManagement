<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('material_group_to_material_id');
	$form->hiddenField('material_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('task_template_to_material_group_id');

	$taskTemplateToMaterialGroup = TaskTemplateToMaterialGroup::model()->findByPk($model->task_template_to_material_group_id);
	
//	if($model->isNewRecord)
//	{
//		// get the default quantity
//		$model->quantity = $assemblyToMaterialGroup->quantity;
//	}

	MaterialController::listWidgetRow($model, $form, 'material_id', array('data-original-title'=>$taskTemplateToMaterialGroup->selection_tooltip), array('scopeMaterialGroup'=>array($model->material_group_id)));

	$form->rangeFieldRow('quantity', $taskTemplateToMaterialGroup->quantity, $taskTemplateToMaterialGroup->minimum, $taskTemplateToMaterialGroup->maximum, $taskTemplateToMaterialGroup->select, $taskTemplateToMaterialGroup->quantity_tooltip);

$this->endWidget();

?>
