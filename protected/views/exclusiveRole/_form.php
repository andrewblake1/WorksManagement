<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	RoleDataController::listWidgetRow($model, $form, 'child_id');

$this->endWidget();

?>