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
					'url'=>$this->createUrl("Project/dependantList"),
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
		
		// if a single option
		if(!empty($model->project_type_id))
		{
			// set some necassary variables - making use of a php quirk here to call non static method via scope resolution operator - ok if not accessing non static member variables
			ProjectController::actionDependantList($model);
			$customFieldsAdded = TRUE;
		}
	}
	else
	{
		$form->hiddenField('project_type_id');
	}

	$form->textFieldRow('travel_time_1_way');

	$form->textFieldRow('critical_completion');

	$form->textFieldRow('planned');
	
	if(!isset($customFieldsAdded))
	{
		$this->widget('CustomFieldWidgets',array(
			'model'=>$model,
			'form'=>$form,
			'relationModelToCustomFieldModelTemplate'=>'projectToProjectTemplateToCustomField',
			'relationModelToCustomFieldModelTemplates'=>'projectToProjectTemplateToCustomFields',
			'relationCustomFieldModelTemplate'=>'projectTemplateToCustomField',
			'relation_category'=>'customFieldProjectCategory',
			'categoryModelName'=>'CustomFieldProjectCategory',
		));
	}

$this->endWidget();

?>
