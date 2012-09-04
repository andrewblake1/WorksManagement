<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description');

	$form->textFieldRow('unit_price');

	$form->textFieldRow('url');

$this->endWidget();

?>