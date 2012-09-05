<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');

	
	// bec
	ProjectTypeToAuthItemController::listWidgetRow($model, $form, 'project_type_to_AuthItem_id', array(), array(
		'scopeTaskTypeToDutyType'=>array($model->task_type_id),
	));

$this->endWidget();

?>
