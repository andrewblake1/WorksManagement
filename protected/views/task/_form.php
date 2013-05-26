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
	if($model->isNewRecord)
	{
		// if a single option
		if(!empty($model->task_template_id))
		{
			// set some necassary variables
			$this->actionDependantList($model);
			$customFieldsAdded = TRUE;
		}
	}

	$this->widget('CustomFieldWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relationModelToCustomFieldModelTemplate'=>'taskToCustomFieldToTaskTemplate',
		'relationModelToCustomFieldModelTemplates'=>'taskToCustomFieldToTaskTemplates',
		'relationCustomFieldModelTemplate'=>'customFieldToTaskTemplate',
		'relation_category'=>'customFieldTaskCategory',
		'categoryModelName'=>'CustomFieldTaskCategory',
	));

$this->endWidget();

?>
