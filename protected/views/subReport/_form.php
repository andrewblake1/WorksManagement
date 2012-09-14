<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description'); 

	$form->textAreaRow('select'); 

	$form->dropDownListRow('format', SubReport::getFormats());

$this->endWidget();

?>