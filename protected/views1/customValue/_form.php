<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('type_int');

	$form->textFieldRow('type_float');

	$form->textFieldRow('type_time');

	$form->textFieldRow('type_date');

	$form->textFieldRow('type_text');

$this->endWidget();

?>
