<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('name');

	$form->hiddenField('type');

	$form->textAreaRow('description');

	$form->hiddenField('bizrule');

	$form->hiddenField('data');

$this->endWidget();

?>
