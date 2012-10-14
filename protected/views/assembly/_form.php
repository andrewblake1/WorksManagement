<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('unit_price');
	
	$form->textFieldRow('alias');
	
$this->endWidget();

?>