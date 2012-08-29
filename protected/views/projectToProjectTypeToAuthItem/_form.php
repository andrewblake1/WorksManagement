<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	AuthAssignmentController::listWidgetRow($model, $form, 'AuthAssignment_id', array(), array(
		'scopeProjectToProjectTypeToAuthItem'=>array($model->project_id),
	));

$this->endWidget();

?>