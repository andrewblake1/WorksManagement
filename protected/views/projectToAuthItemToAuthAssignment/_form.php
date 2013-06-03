<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	AuthAssignmentController::listWidgetRow($model, $form, 'auth_assignment_id', array(), array(
		'scopeProjectToAuthItemId' => array($model->project_to_auth_item_id),
	), 'User');

$this->endWidget();

?>