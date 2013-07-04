<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('maximum');

	$form->textFieldRow('unit_price');

	$form->dropDownListRow('level', Planning::$levels);

$this->endWidget();

?>