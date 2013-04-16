<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('material_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('assembly_to_material_group_id');

	$assemblyToMaterialGroup = AssemblyToMaterialGroup::model()->findByPk($model->assembly_to_material_group_id);
	
	if($model->isNewRecord)
	{
		// get the default quantity
		$model->quantity = $assemblyToMaterialGroup->quantity;
	}

	MaterialController::listWidgetRow($model, $form, 'material_id', array('data-original-title'=>$assemblyToMaterialGroup->selection_tooltip), array('scopeMaterialGroup'=>array($model->material_group_id)));

	$htmlOptions = array('data-original-title'=>$assemblyToMaterialGroup->quantity_tooltip);
	if(empty($assemblyToMaterialGroup->select))
	{
		$form->rangeFieldRow('quantity', $assemblyToMaterialGroup->minimum, $assemblyToMaterialGroup->maximum, $htmlOptions, $model);
	}
	else
	{
		// first need to get a list where array keys are the same as the display members
		$list = explode(',', $assemblyToMaterialGroup->select);

		$form->dropDownListRow('quantity', array_combine($list, $list), $htmlOptions, $model);
	}
	

$this->endWidget();

?>
