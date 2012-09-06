<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	AuthItemController::listWidgetRow($model, $form, 'itemname', array(), array('roles'));

	$form->hiddenField('bizrule');

	$form->hiddenField('data');

$this->endWidget();

?>