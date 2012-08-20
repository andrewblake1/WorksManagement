<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->task_type_id))
	{
		$form->hiddenField('task_type_id');
	}
	else
	{
		TaskTypeController::listWidgetRow($model, $form, 'task_type_id');
	}

	GenerictaskcategoryController::listWidgetRow($model, $form, 'generictaskcategory_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
