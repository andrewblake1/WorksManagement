<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('description'); 

	$form->textAreaRow('select'); 

$this->endWidget();

?>