<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'action'=>empty($action) ? null : $action, 
	'parent_fk'=>$parent_fk,
));

	$form->hiddenField('duty_step_id');
	
	$form->textFieldRow('name');
	
	// if adding to another node - as opposed to creating a new root
	echo '<input type="hidden" name= "parent_id" value="'.(empty($_POST['parent_id']) ? '' : $_POST['parent_id']).'" />';

	$form->hiddenField('client_id');
	$form->hiddenField('project_template_id');
	$form->hiddenField('action_id');

$this->endWidget();

?>
