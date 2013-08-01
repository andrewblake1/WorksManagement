<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	$form->dropDownListRow('level', Planning::$levels);

	AuthItemController::listWidgetRow($model, $form, 'auth_item_name', array(), array('roles'));

	$form->textFieldRow('quantity');

	$form->textFieldRow('estimated_total_quantity');

	ModeController::listWidgetRow($model, $form, 'mode_id');

$this->endWidget();

?>