<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	$form->textFieldRow('maximum');

	$form->textFieldRow('unit_price');

$this->endWidget();

?>
