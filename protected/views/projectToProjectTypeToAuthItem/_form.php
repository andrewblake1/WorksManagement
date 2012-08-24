<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if(isset($model->project_id))
	{
		$form->hiddenField('project_id');
	}
	else
	{
		ProjectController::listWidgetRow($model, $form, 'project_id');
	}
// if project id is set then restrict these in beforeValidate
//	ProjectTypeToAuthItemController::listWidgetRow($model, $form, 'project_type_to_AuthItem_id');

	ProjectToProjectTypeToAuthItem::$niceName = 'Role to staff';
	
	AuthAssignmentController::listWidgetRow($model, $form, 'AuthAssignment_id', array(), array(
		'scopeProjectToProjectTypeToAuthItem'=>array($model->project_id),
	));

	// itemname in part of circular foreign key and therefore gets assigned based on AuthAssignment_id in beforeValidate
//	AuthItemController::listWidgetRow($model, $form, 'itemname');

$this->endWidget();

?>