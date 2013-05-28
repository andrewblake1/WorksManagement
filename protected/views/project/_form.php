<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'models'=>$models,
	'action'=>empty($action) ? null : $action, 
	'parent_fk'=>$parent_fk
));

	$form->textFieldRow('name');

	// only allow setting or update of in_charge_id if user has InCharge privilege
	if(Yii::app()->user->checkAccess('scheduler'))
	{
		UserController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');
	}

	// if creating
	if($model->isNewRecord)
	{
		ProjectTypeController::listWidgetRow($model, $form, 'project_type_id',
			array(
				'empty'=>'Please select',
				'ajax' => array(
					'type'=>'POST',
					'url'=>$this->createUrl("dependantList"),
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
			array('scopeClient'=>array($model->client_id))
		);
		
	}
	else
	{
		$form->hiddenField('project_type_id');
	}

	$form->textFieldRow('travel_time_1_way');

	$form->textFieldRow('critical_completion');

	$form->textFieldRow('planned');
	
	// customValues
	if($model->isNewRecord)
	{
		// if a single option
		if(!empty($model->project_type_id))
		{
			// set some necassary variables
			$this->actionDependantList($model);
			$customFieldsAdded = TRUE;
		}
	}

	if(!isset($customFieldsAdded))
	{
		$this->widget('CustomFieldWidgets',array(
			'model'=>$model,
			'form'=>$form,
			'relationModelToCustomFieldModelTemplate'=>'projectToCustomFieldToProjectTemplate',
			'relationModelToCustomFieldModelTemplates'=>'projectToCustomFieldToProjectTemplates',
			'relationCustomFieldModelTemplate'=>'customFieldToProjectTemplate',
			'relation_category'=>'customFieldProjectCategory',
			'categoryModelName'=>'CustomFieldProjectCategory',
		));
	}

$this->endWidget();

?>
