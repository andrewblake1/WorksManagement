<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AuthAssignmentController::listWidgetRow($model, $form, 'AuthAssignment_id', array(), array(
		'scopeProjectToProjectTypeToAuthItem'=>array($model->project_id),
	));

$this->endWidget();

?>