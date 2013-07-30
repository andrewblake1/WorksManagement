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
							$('#customValues').hide('slow', function() {
								$('#customValues').replaceWith(data);
								$('#customValues').hide('slow', function() {
									$('#customValues').show('slow');
								});
							});
						}
						// clean it out
						else
						{
							$('#customValues').hide('slow', function() {
								$('#customValues').html('');
								$('#customValues').hide('slow', function() {
									$('#customValues').show('slow');
								});
							});
						}
					}",
				)
			),
			array('scopeCrew'=>array($model->crew_id))
		);
	}
	else
	{
		$form->hiddenField('task_template_id');
		$taskTemplate = $model->taskTemplate;
		$form->rangeFieldRow('quantity', $taskTemplate->quantity, $taskTemplate->minimum, $taskTemplate->maximum, $taskTemplate->select, $taskTemplate->quantity_tooltip);
	}

	$form->textFieldRow('name');

	$form->textAreaRow('location');

	// only allow setting or update of in_charge_id if user has correct priveledge
	if(Yii::app()->user->checkAccess('scheduler'))
	{
		UserController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');
	}

	$form->textFieldRow('planned');

	$form->checkBoxListInlineRow('preferred', array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'));

	// customValues
	if($model->isNewRecord)
	{
		// if a single option
		if(!empty($model->task_template_id))
		{
			// set some necassary variables - making use of a php quirk here to call non static method via scope resolution operator - ok if not accessing non static member variables
			TaskController::actionDependantList($model);
			$customFieldsAdded = TRUE;
		}
	}

	ModeController::listWidgetRow($model, $form, 'mode_id');

	$this->widget('CustomFieldWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relationModelToCustomFieldModelTemplate'=>'taskToTaskTemplateToCustomField',
		'relationModelToCustomFieldModelTemplates'=>'taskToTaskTemplateToCustomFields',
		'relationCustomFieldModelTemplate'=>'taskTemplateToCustomField',
		'relation_category'=>'customFieldTaskCategory',
		'categoryModelName'=>'CustomFieldTaskCategory',
	));

$this->endWidget();

?>
