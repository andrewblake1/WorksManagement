<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('assembly_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('assembly_to_assembly_group_id');

	$assemblyToAssemblyGroup = AssemblyToAssemblyGroup::model()->findByPk($model->assembly_to_assembly_group_id);
	
	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array('data-original-title'=>$assemblyToAssemblyGroup->selection_tooltip), array('scopeAssemblyGroup'=>array($model->assembly_group_id)));

	$form->rangeFieldRow('quantity', $assemblyToAssemblyGroup->minimum, $assemblyToAssemblyGroup->maximimum, $assemblyToAssemblyGroup->select, $assemblyToAssemblyGroup->quantity_tooltip);

$this->endWidget();

?>
