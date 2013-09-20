<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'action'=>$action));		

	$form->hiddenField('material_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_template_to_material_group_id');

	$taskTemplateToMaterialGroup = TaskTemplateToMaterialGroup::model()->findByPk($model->task_template_to_material_group_id);
	
	MaterialController::listWidgetRow($model, $form, 'material_id', array('data-original-title'=>$taskTemplateToMaterialGroup->selection_tooltip), array('scopeMaterialGroup'=>array($model->material_group_id)));

	$form->rangeFieldRow('quantity', $taskTemplateToMaterialGroup->quantity, $taskTemplateToMaterialGroup->minimum, $taskTemplateToMaterialGroup->maximum, $taskTemplateToMaterialGroup->select, $taskTemplateToMaterialGroup->quantity_tooltip);

$this->endWidget();

?>
