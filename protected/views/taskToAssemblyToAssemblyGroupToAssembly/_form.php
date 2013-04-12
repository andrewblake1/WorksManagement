<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));		

	$form->hiddenField('assembly_group_id');
	$form->hiddenField('task_id');
	$form->hiddenField('task_to_assembly_id');
	$form->hiddenField('assembly_to_assembly_group_id');

	$assemblyToAssemblyGroup = AssemblyToAssemblyGroup::model()->findByPk($model->assembly_to_assembly_group_id);
	$quantity_tooltip = $assemblyToAssemblyGroup->quantity_tooltip;
	
	if($model->isNewRecord)
	{
		// get the default quantity
		$model->quantity = $assemblyToAssemblyGroup->quantity;
	}
	else
	{
		$form->hiddenField('assembly_id');
		// otherwise our previous saved quantity
		$taskToAssemblyId = TaskToAssembly::model()->findByPk($model->task_to_assembly_id);
		$model->quantity = $taskToAssemblyId->quantity;
	}

	AssemblyController::listWidgetRow($model, $form, 'assembly_id', array('data-original-title'=>$assemblyToAssemblyGroup->selection_tooltip), array('scopeAssemblyGroup'=>array($model->assembly_group_id)));

	$form->textFieldRow('quantity', array('data-original-title'=>$assemblyToAssemblyGroup->quantity_tooltip));

$this->endWidget();

?>
