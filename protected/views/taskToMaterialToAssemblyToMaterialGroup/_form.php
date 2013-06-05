<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('material_group_to_material_id');
	$form->hiddenField('material_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('assembly_to_material_group_id');

	$assemblyToMaterialGroup = AssemblyToMaterialGroup::model()->findByPk($model->assembly_to_material_group_id);
	
//	if($model->isNewRecord)
//	{
//		// get the default quantity
//		$model->quantity = $assemblyToMaterialGroup->quantity;
//	}

	MaterialController::listWidgetRow($model, $form, 'material_id', array('data-original-title'=>$assemblyToMaterialGroup->selection_tooltip), array('scopeMaterialGroup'=>array($model->material_group_id)));

	$form->rangeFieldRow('quantity', $assemblyToMaterialGroup->quantity, $assemblyToMaterialGroup->minimum, $assemblyToMaterialGroup->maximum, $assemblyToMaterialGroup->select, $assemblyToMaterialGroup->quantity_tooltip);

$this->endWidget();

?>
