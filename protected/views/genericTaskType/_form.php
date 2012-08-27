<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->task_type_id))
	{
		$form->hiddenField('task_type_id');
	}
	else
	{
		throw new CHttpException(400, 'No task type identified, you must get here from the task types page');
	}

	GenerictaskcategoryController::listWidgetRow($model, $form, 'generictaskcategory_id');

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id');

$this->endWidget();

?>
