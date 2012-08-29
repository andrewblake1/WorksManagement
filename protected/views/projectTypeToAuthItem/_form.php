<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));
	
	AuthItemController::listWidgetRow($model, $form, 'AuthItem_name', array(), array('roles'));

$this->endWidget();

?>