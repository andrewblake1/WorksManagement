<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	TaskTemplateToRoleController::listWidgetRow($model, $form, 'child_id');

$this->endWidget();

?>