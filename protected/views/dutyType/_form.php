<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	$form->textFieldRow('lead_in_days');

	$form->dropDownListRow('level', Schedule::getLevels());

	GenericTypeController::listWidgetRow($model, $form, 'generic_type_id', array(), array(), null);

$this->endWidget();

?>