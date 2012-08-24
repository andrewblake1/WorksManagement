<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('name');

	$form->hiddenField('type');

	$form->textAreaRow('description');

	$form->hiddenField('bizrule');

	$form->hiddenField('data');

$this->endWidget();

?>
