<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	if($model->isNewRecord)
	{
		CustomFieldToProjectTemplateController::listWidgetRow($model, $form, 'custom_field_to_project_template_id');
	}
	else
	{
		$this->widget('CustomFieldWidget', array(
			'form'=>$form,
			'customValue'=>$model->customValue,
			'customField'=>$model->customFieldToProjectTemplate->customField,
			'relationToCustomField'=>'projectToCustomFieldToProjectTemplate->customFieldToProjectTemplate->customField'
		));
	}

$this->endWidget();

?>