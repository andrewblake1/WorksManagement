<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('lead_in_days');

	$form->dropDownListRow('level', Schedule::$levels);

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id', array(), array(), null);

$this->endWidget();

?>