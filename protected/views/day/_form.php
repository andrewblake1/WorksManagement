<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	$form->textFieldRow('scheduled');

	$form->textFieldRow('preferred');

	$form->textFieldRow('earliest');

	$form->textFieldRow('planned');

$this->endWidget();

?>
