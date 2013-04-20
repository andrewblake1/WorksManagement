<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('assembly_group_to_assembly_id');
	$form->hiddenField('assembly_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('task_type_to_assembly_group_id');

	$taskTypeToAssemblyGroup = TaskTypeToAssemblyGroup::model()->findByPk($model->task_type_to_assembly_group_id);
	
	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array('data-original-title'=>$taskTypeToAssemblyGroup->selection_tooltip), array('scopeAssemblyGroup'=>array($model->assembly_group_id)));

	$form->rangeFieldRow('quantity', $taskTypeToAssemblyGroup->minimum, $taskTypeToAssemblyGroup->maximimum, $taskTypeToAssemblyGroup->select, $taskTypeToAssemblyGroup->quantity_tooltip);

$this->endWidget();

?>x
