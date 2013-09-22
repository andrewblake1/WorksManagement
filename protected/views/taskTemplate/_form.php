<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('alias');

	$form->textFieldRow('comment');

	$form->textFieldRow('unit_price');

	$form->textFieldRow('quantity');

	$form->textFieldRow('minimum');

	$form->textFieldRow('maximum');

	$form->textAreaRow('quantity_tooltip');
	
	$form->textAreaRow('select');

$this->endWidget();

?>
