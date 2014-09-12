<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->hiddenField('action_to_plant_id');
 
	$form->textFieldRow('quantity');

	$form->textFieldRow('duration');

$this->endWidget();

?>