<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'action'=>empty($action) ? null : $action, 
));

	// only show when creating
	if($model->isNewRecord)
	{
		TaskTemplateController::listWidgetRow($model, $form, 'task_template_id',
			array(
				'empty'=>'Please select',
				'ajax' => array(
					'type'=>'POST',
					'url'=>$this->createUrl("Task/dependantList"),
					// animate the replacement
					'success'=>"function(data) {
						if(data)
						{
							$('#templateDependantArea').hide('slow', function() {
								$('#templateDependantArea').replaceWith(data);
								$('#templateDependantArea').hide('slow', function() {
									$('#templateDependantArea').show('slow');
								});
							});
						}
						else // clean it out
						{
							$('#templateDependantArea').hide('slow', function() {
								$('#templateDependantArea').html('');
								$('#templateDependantArea').hide('slow', function() {
									$('#templateDependantArea').show('slow');
								});
							});
						}
					}",
				)
			),
			array('scopeCrew'=>array($model->crew_id))
		);

		// if a single option
		if(!empty($model->task_template_id))
		{
			// set some necassary variables - making use of a php quirk here to call non static method via scope resolution operator - ok if not accessing non static member variables
			TaskController::actionDependantList($model);
			$customFieldsAdded = TRUE;
		}
	}
	else
	{
		$form->hiddenField('task_template_id');
	}

	$form->textFieldRow('name');

	// only allow setting or update of in_charge_id if user has correct privilege
	if(Yii::app()->user->checkAccess('scheduler'))
	{
		UserController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');
	}

	$form->datepickerRow('critical_completion');

	$form->textFieldRow('planned');

	ModeController::listWidgetRow($model, $form, 'mode_id');

	if(!isset($customFieldsAdded))
	{
		echo CHtml::openTag('div', array('id'=>'templateDependantArea'));
	
		// quantity
		if($taskTemplate = $model->taskTemplate)
		{
			$form->rangeFieldRow('quantity', $taskTemplate->quantity, $taskTemplate->minimum, $taskTemplate->maximum, $taskTemplate->select, $taskTemplate->quantity_tooltip);
		}

		// custom fields
		$this->widget('CustomFieldWidgets',array(
			'model'=>$model,
			'form'=>$form,
			'relationModelToCustomFieldModelTemplate'=>'taskToTaskTemplateToCustomField',
			'relationModelToCustomFieldModelTemplates'=>'taskToTaskTemplateToCustomFields',
			'relationCustomFieldModelTemplate'=>'taskTemplateToCustomField',
			'relation_category'=>'customFieldTaskCategory',
			'categoryModelName'=>'CustomFieldTaskCategory',
		));

		echo CHtml::closeTag('div');
	}

$this->endWidget();

?>
