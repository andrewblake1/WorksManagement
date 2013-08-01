<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	AuthItemController::listWidgetRow($model, $form, 'auth_item_name');

	$form->textFieldRow('unit_price');

	$form->dropDownListRow('level', Planning::$levels);

	ActionController::listWidgetRow($model, $form, 'action_id');

$this->endWidget();

?>