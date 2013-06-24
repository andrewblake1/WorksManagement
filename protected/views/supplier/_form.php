<?php

$form=$this->beginWidget('WMTbFileUploadActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('name');

$this->endWidget();

?>
