<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'action'=>$action));		

	$form->hiddenField('assembly_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('task_template_to_assembly_group_id');

	$taskTemplateToAssemblyGroup = TaskTemplateToAssemblyGroup::model()->findByPk($model->task_template_to_assembly_group_id);
	
	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array('data-original-title'=>$taskTemplateToAssemblyGroup->selection_tooltip), array('scopeAssemblyGroup'=>array($model->assembly_group_id)));

	$form->rangeFieldRow('quantity', $taskTemplateToAssemblyGroup->quantity, $taskTemplateToAssemblyGroup->minimum, $taskTemplateToAssemblyGroup->maximum, $taskTemplateToAssemblyGroup->select, $taskTemplateToAssemblyGroup->quantity_tooltip);

$this->endWidget();

?>
