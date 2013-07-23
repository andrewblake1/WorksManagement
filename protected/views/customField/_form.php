<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('label');

	$form->checkBoxRow('mandatory');	

	$form->checkBoxRow('allow_new');

	$form->dropDownListRow('validation_type', $model->validationTypeLabels);
	
	$form->dropDownListRow('data_type', $model->dataTypeLabels);

	$form->textAreaRow('validation_text');

	$form->textAreaRow('validation_error');

$this->endWidget();

?>
