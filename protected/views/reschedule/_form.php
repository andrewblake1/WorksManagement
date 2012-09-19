<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->datepickerRow('scheduled');

$this->endWidget();

?>
