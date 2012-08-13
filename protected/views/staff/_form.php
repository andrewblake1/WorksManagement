<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('first_name');

	$form->textFieldRow('last_name');

	$form->textFieldRow('phone_mobile');

	$form->textFieldRow('email');

	$form->passwordFieldRow('password');

$this->endWidget();

?>
