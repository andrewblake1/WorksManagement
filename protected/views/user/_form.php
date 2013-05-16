<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('first_name');

	$form->textFieldRow('last_name');

	$form->textFieldRow('phone_mobile');

	$form->textFieldRow('email');

	$form->passwordFieldRow('password');

$this->endWidget();

?>
