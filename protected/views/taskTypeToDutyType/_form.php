<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');

	if(isset($model->task_type_id))
	{
		$form->hiddenField('task_type_id');
	}
	else
	{
		TaskTypeController::listWidgetRow($model, $form, 'task_type_id');
	}

	AuthItemController::listWidgetRow($model, $form, 'AuthItem_name');

$this->endWidget();

?>
