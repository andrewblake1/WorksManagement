<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('first_name');
	$form->textFieldRow('last_name');
	$form->textFieldRow('role');
	$form->textFieldRow('email');
	$form->textFieldRow('address_line1');
	$form->textFieldRow('address_line2');
	$form->textFieldRow('post_code');
	$form->textFieldRow('town_city');
	$form->textFieldRow('state_province');
	$form->textFieldRow('country');
	$form->textFieldRow('phone_mobile');
	$form->textFieldRow('phone_home');
	$form->textFieldRow('phone_work');
	$form->textFieldRow('phone_fax');

$this->endWidget();

?>