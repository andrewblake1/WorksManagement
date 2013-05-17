<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'models'=>$models,
	'action'=>empty($action) ? null : $action, 
	'parent_fk'=>$parent_fk
));

	$form->textFieldRow('name');

	// only allow setting or update of in_charge_id if user has InCharge priveledge
	if(Yii::app()->user->checkAccess('scheduler'))
	{
		UserController::listWidgetRow($model->id0 ? $model->id0 : new Planning, $form, 'in_charge_id', array(), array(), 'In charge');
	}

	// if creating
	if($model->isNewRecord)
	{
		ProjectTemplateController::listWidgetRow($model, $form, 'project_template_id',
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
						/* clean it out */
						else
						{
							$('#customValues').html('');
						}
					}",
				)
			),
			array('scopeClient'=>array($model->client_id))
		);
	}
	else
	{
		$form->hiddenField('project_template_id');
	}

	$form->textFieldRow('travel_time_1_way');

	$form->textFieldRow('critical_completion');

	$form->textFieldRow('planned');
	
	// customValues
	$this->widget('CustomFieldWidgets',array(
		'model'=>$model,
		'form'=>$form,
		'relationModelToCustomFieldModelType'=>'projectToCustomFieldToProjectTemplate',
		'relationModelToCustomFieldModelTypes'=>'projectToCustomFieldToProjectTemplates',
		'relationCustomFieldModelType'=>'customFieldToProjectTemplate',
		'relation_category'=>'customFieldProjectCategory',
		'categoryModelName'=>'CustomFieldProjectCategory',
	));

$this->endWidget();

?>
