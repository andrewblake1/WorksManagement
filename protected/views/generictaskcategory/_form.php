<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('root');

	$form->textFieldRow('lft');

	$form->textFieldRow('rgt');

	$form->textFieldRow('level');

	$form->textFieldRow('description');

$this->endWidget();

?>
