<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textAreaRow('description');

	// only show when creating
	if($model->isNewRecord)
		TaskTypeController::listWidgetRow($model, $form, 'task_type_id');

	$form->textFieldRow('day');

	PurchaseOrderController::listWidgetRow($model, $form, 'purchase_order_id');

	CrewController::listWidgetRow($model, $form, 'crew_id');

	ProjectController::listWidgetRow($model, $form, 'project_id');
	
	 // generics
	$this->widget('GenericWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relation_modelToGenericModelType'=>'taskToGenericTaskType',
		'relation_modelToGenericModelTypes'=>'taskToGenericTaskTypes',
		'relation_genericModelType'=>'genericTaskType',
	));

/*	// resources
	// only show when updating
	if(!$model->isNewRecord)
		$this->widget('ResourceWidgets',array(
			'model'=>$model,
			'form'=>$form,
		));*/

$this->endWidget();

?>
