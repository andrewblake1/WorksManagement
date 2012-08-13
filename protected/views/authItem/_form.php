<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('name');

	$form->textFieldRow('type');

	$form->textAreaRow('description');

	$form->textAreaRow('bizrule');

	$form->textAreaRow('data');

$this->endWidget();

?>
