<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if($model->isNewRecord)
	{
		GenericTaskTypeController::listWidgetRow($model, $form, 'generic_task_type_id');
	}
	else
	{
		$this->widget('GenericWidget', array(
			'form'=>$form,
			'generic'=>$model->generic,
			'genericType'=>$model->genericTaskType->genericType,
			'relationToGenericType'=>'taskToGenericTaskType->genericTaskType->genericType'
		));
	}

$this->endWidget();

?>
