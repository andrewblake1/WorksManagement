<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('lead_in_days');

	$form->dropDownListRow('level', Planning::$levels);

	CustomFieldController::listWidgetRow($model, $form, 'custom_field_id', array(), array(), null);

	$form->textAreaRow('comment');
	
$this->endWidget();

?>