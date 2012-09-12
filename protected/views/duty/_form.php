<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

//	TaskTypeToDutyTypeController::listWidgetRow($model, $form, 'task_type_to_duty_type_id', array(), array('scopeTask'=>array($model->task_id)));

	// if previously saved
	if($model->updated)
	{
		$form->textFieldRow('updated', Yii::app()->user->checkAccess('system admin') ? array() : array('readonly'=>'readonly'));
	}
	else
	{
		$form->checkBoxRow('updated');
	}

	if(!empty($model->dutyData->generic_id))
	{
		$this->widget('GenericWidget', array(
			'form'=>$form,
			'generic'=>$model->dutyData->generic,
			'genericType'=>$model->taskTypeToDutyType->dutyType->genericType,
			'relationToGenericType'=>'duty->taskTypeToDutyType->dutyType->genericType',
		));
	}

$this->endWidget();

?>
