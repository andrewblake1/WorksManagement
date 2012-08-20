<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textAreaRow('description');

	// only show when creating
	if($model->isNewRecord)
	{
		if(!isset($model->project_id))
		{
			ProjectController::listWidgetRow($model, $form, 'project_id');
		}
		else
		{
			$form->hiddenField('project_id');
		}
		TaskTypeController::listWidgetRow($model, $form, 'task_type_id');
	}
	else
	{
		$form->hiddenField('task_type_id');
		$form->hiddenField('project_id');
	}

	PurchaseOrderController::listWidgetRow($model, $form, 'purchase_order_id');

	StaffController::listWidgetRow($model, $form, 'in_charge_id');

	$form->textFieldRow('planned');
	
	$form->textFieldRow('scheduled');
	
	$form->textFieldRow('earliest');
	
	$form->textFieldRow('preferred');

	
/*	 // generics
	$this->widget('GenericWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relation_modelToGenericModelType'=>'taskToGenericTaskType',
		'relation_modelToGenericModelTypes'=>'taskToGenericTaskTypes',
		'relation_genericModelType'=>'genericTaskType',
	));

	// resources
	// only show when updating
	if(!$model->isNewRecord)
		$this->widget('ResourceWidgets',array(
			'model'=>$model,
			'form'=>$form,
		));*/

$this->endWidget();

?>
