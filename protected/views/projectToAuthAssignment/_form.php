<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->project_id))
	{
		$form->hiddenField('project_id');
	}
	else
	{
		AuthAssignmentController::listWidgetRow($model, $form, 'project_id');
	}	

	AuthAssignmentController::listWidgetRow($model, $form, 'AuthAssignment_id');

$this->endWidget();

?>
