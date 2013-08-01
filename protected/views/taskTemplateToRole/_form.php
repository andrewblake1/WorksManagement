<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	AuthItemController::listWidgetRow($model, $form, 'auth_item_name', array(), array('roles'));

	ModeController::listWidgetRow($model, $form, 'mode_id');

	$form->textFieldRow('quantity');

	$form->dropDownListRow('level', Planning::$levels);

$this->endWidget();

?>