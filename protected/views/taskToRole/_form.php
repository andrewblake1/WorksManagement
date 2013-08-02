<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	$form->dropDownListRow('level', Planning::$levels);

	HumanResourceController::listWidgetRow($model, $form, 'human_resource_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('estimated_total_quantity');

//	ModeController::listWidgetRow($model, $form, 'mode_id');

$this->endWidget();

?>