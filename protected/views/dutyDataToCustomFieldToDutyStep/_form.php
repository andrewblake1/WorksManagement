<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	if($model->isNewRecord)
	{
		CustomFieldToDutyStepController::listWidgetRow($model, $form, 'custom_field_to_duty_step_id');
	}
	else
	{
		$this->widget('CustomFieldWidget', array(
			'form'=>$form,
			'customValue'=>$model->customValue,
			'customField'=>$model->customFieldToDutyStep->customField,
			'relationToCustomField'=>'projectToCustomFieldToDutyStep->customFieldToDutyStep->customField'
		));
	}

$this->endWidget();

?>