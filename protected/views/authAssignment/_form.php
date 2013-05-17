<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AuthItemController::listWidgetRow($model, $form, 'itemname', array(), array('roles'));

	$form->hiddenField('bizrule');

	$form->hiddenField('data');

$this->endWidget();

?>