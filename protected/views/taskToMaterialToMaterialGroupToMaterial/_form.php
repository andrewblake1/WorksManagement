<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('material_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('assembly_to_material_group_id');

	$assemblyToMaterialGroup = AssemblyToMaterialGroup::model()->findByPk($model->assembly_to_material_group_id);
	$quantity_tooltip = $assemblyToMaterialGroup->quantity_tooltip;
	
	if($model->isNewRecord)
	{
		// get the default quantity
		$model->quantity = $assemblyToMaterialGroup->quantity;
	}
	else
	{
		$form->hiddenField('material_id');
		// otherwise our previous saved quantity
		$taskToMaterialId = TaskToMaterial::model()->findByPk($model->task_to_material_id);
		$model->quantity = $taskToMaterialId->quantity;
	}

	MaterialController::listWidgetRow($model, $form, 'material_id', array('data-original-title'=>$assemblyToMaterialGroup->selection_tooltip), array('scopeMaterialGroup'=>array($model->material_group_id)));

	$form->textFieldRow('quantity', array('data-original-title'=>$assemblyToMaterialGroup->quantity_tooltip));

$this->endWidget();

?>
