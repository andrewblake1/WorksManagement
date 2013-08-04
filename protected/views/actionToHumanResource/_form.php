<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	HumanResourceController::listWidgetRow($model, $form, 'human_resource_id');

	ModeController::listWidgetRow($model, $form, 'mode_id');

	$form->dropDownListRow('level', Planning::$levels);

	$form->textFieldRow('quantity');

$this->endWidget();

?>