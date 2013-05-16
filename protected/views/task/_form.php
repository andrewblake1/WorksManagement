<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'action'=>empty($action) ? null : $action, 
));

	$form->textFieldRow('name');

	$form->textAreaRow('location');

	// only show when creating
	if($model->isNewRecord)
	{
		TaskTemplateController::listWidgetRow($model, $form, 'task_template_id',
			array(
				'empty'=>'Please select',
				'ajax' => array(
					'type'=>'POST',
					'url'=>$this->createUrl("dependantList"),
					'success'=>"function(data) {
						if(data)
						{
							$('#customValues').replaceWith(data);
						}
					}",
				)
			),
			array('scopeProjectTemplate'=>array($model->crew_id))
		);
	}
	else
	{
		$form->hiddenField('task_template_id');
		$taskTemplate = $model->taskTemplate;
		$form->rangeFieldRow('quantity', $taskTemplate->minimum, $taskTemplate->maximum, $taskTemplate->select, $taskTemplate->quantity_tooltip);
	}

	// only allow setting or update of in_charge_id if user has correct priveledge
	if(Yii::app()->user->checkAccess('scheduler'))
	{
		UserController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');
	}


	$form->textFieldRow('planned');

//	$form->textFieldRow('earliest')				;

	$form->checkBoxListInlineRow('preferred', array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'));

	 // customValues
	$this->widget('CustomFieldWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relationModelToCustomFieldModelType'=>'taskToCustomFieldToTaskTemplate',
		'relationModelToCustomFieldModelTypes'=>'taskToCustomFieldToTaskTemplates',
		'relationCustomFieldModelType'=>'customFieldToTaskTemplate',
		'relation_category'=>'customFieldTaskCategory',
		'categoryModelName'=>'CustomFieldTaskCategory',
	));

$this->endWidget();

?>
