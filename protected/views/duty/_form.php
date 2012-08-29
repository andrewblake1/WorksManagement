<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

//	TaskTypeToDutyTypeController::listWidgetRow($model, $form, 'task_type_to_duty_type_id', array(), array('scopeTask'=>array($model->task_id)));

	// if previously saved
	if($model->updated)
	{
		$form->textFieldRow('updated', array('readonly'=>'readonly'));
	}
	else
	{
		$form->checkBoxRow('updated');
	}

	if(!empty($this->model->generic_id))
	{
		$this->controller->widget('GenericWidget', array(
			'form'=>$this->form,
			'generic'=>$this->model->generic,
			'genericType'=>$this->model->taskTypeToDutyType->dutyType->genericType,
			'relationToGenericType'=>"duty->taskTypeToDutyType->dutyType->genericType",
		));
	}

$this->endWidget();

?>
