<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));		

	
	// if part of sub assembly
	if(!$model->isNewRecord && $model->taskToAssembly)
	{
		$form->hiddenField('material_id');
		// material is alterable but might control quantity
		$assemblyToMaterial = TaskToMaterialToAssemblyToMaterial::model()->findByAttributes(array('task_to_material_id' => $model->id))->assemblyToMaterial;
		$form->rangeFieldRow('quantity', $assemblyToMaterial->quantity, $assemblyToMaterial->minimum, $assemblyToMaterial->maximum, $assemblyToMaterial->select, $assemblyToMaterial->quantity_tooltip);
	}
	else
	{
		MaterialController::dependantListWidgetRow($model, $form, 'material_id', 'Standard', 'standard_id', array(), array('scopeStandard'=>array($model->standard_id === null ? 0 : $model->standard_id)));
		$form->textFieldRow('quantity');
	}

$this->endWidget();

?>
