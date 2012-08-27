<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');

	if(isset($model->task_type_id))
	{
		$form->hiddenField('task_type_id');
	}
	else
	{
		throw new CHttpException(400, 'No client identified, you must get here from the task types page');
	}

	ProjectTypeToAuthItemController::listWidgetRow($model, $form, 'project_type_to_AuthItem_id');

$this->endWidget();

?>
