<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	AuthItemController::listWidgetRow($model, $form, 'AuthItem_name', array(), array('roles'));

$this->endWidget();

?>