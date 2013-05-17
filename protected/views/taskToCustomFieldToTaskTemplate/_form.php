<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	if($model->isNewRecord)
	{
		CustomFieldToTaskTemplateController::listWidgetRow($model, $form, 'custom_field_to_task_template_id');
	}
	else
	{
		$this->widget('CustomFieldWidget', array(
			'form'=>$form,
			'customValue'=>$model->customValue,
			'customField'=>$model->customFieldToTaskTemplate->customField,
			'relationToCustomField'=>'taskToCustomFieldToTaskTemplate->customFieldToTaskTemplate->customField'
		));
	}

$this->endWidget();

?>
