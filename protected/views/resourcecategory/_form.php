<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'action'=>$action));

	$form->textFieldRow('name');

	DutycategoryController::listWidgetRow($model, $form, 'dutycategory_id');
	
	// if adding to another node - as opposed to creating a new root
	echo '<input type="hidden" name= "parent_id" value="'.($_POST['parent_id'] ? $_POST['parent_id']  : '').'" />';

$this->endWidget();

?>