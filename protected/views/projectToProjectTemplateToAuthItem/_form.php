<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AuthAssignmentController::listWidgetRow($model, $form, 'auth_assignment_id', array(), array(
		'scopeProjectToProjectTemplateToAuthItem'=>array($model->project_id),
	));

$this->endWidget();

?>