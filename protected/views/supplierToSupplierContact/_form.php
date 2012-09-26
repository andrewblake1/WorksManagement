<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

//	SupplierContactController::listWidgetRow($model, $form, 'supplier_contact_id');

	$SupplierContact = empty($model->supplierContact) ? new SupplierContact : $model->supplierContact;
	$form->textFieldRow('first_name', array(), $SupplierContact);
	$form->textFieldRow('last_name', array(), $SupplierContact);
	$form->textFieldRow('email', array(), $SupplierContact);
	$form->textFieldRow('address_line1', array(), $SupplierContact);
	$form->textFieldRow('address_line2', array(), $SupplierContact);
	$form->textFieldRow('post_code', array(), $SupplierContact);
	$form->textFieldRow('town_city', array(), $SupplierContact);
	$form->textFieldRow('state_province', array(), $SupplierContact);
	$form->textFieldRow('country', array(), $SupplierContact);
	$form->textFieldRow('phone_mobile', array(), $SupplierContact);
	$form->textFieldRow('phone_home', array(), $SupplierContact);
	$form->textFieldRow('phone_work', array(), $SupplierContact);
	$form->textFieldRow('phone_fax', array(), $SupplierContact);
	
$this->endWidget();

?>