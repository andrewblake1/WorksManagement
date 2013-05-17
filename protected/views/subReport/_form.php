<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description'); 

	$form->textAreaRow('select'); 

	$form->dropDownListRow('format', SubReport::getFormats());

$this->endWidget();

?>