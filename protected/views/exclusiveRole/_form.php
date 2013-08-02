<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	RoleDataController::listWidgetRow($model, $form, 'child_id', array(), array('scopePlanning'=>array($model->parent_id, $model->parent->planning_id, $model->parent->mode_id)));

$this->endWidget();

?>