<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	HumanResourceController::listWidgetRow($model, $form, 'human_resource_id');

	ModeController::listWidgetRow($model, $form, 'mode_id');

	$form->textFieldRow('quantity');

	$form->dropDownListRow('level', Planning::$levels);

$this->endWidget();

?>