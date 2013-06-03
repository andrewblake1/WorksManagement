<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'action'=>empty($action) ? null : $action, 
	'parent_fk'=>$parent_fk,
));

	$form->hiddenField('task_template_id');
	
	$form->textFieldRow('name');
	
//	$form->checkBoxRow('show_in_admin');	

//	$form->checkBoxRow('show_in_planning');	

	// if adding to another node - as opposed to creating a new root
	echo '<input type="hidden" name= "parent_id" value="'.(empty($_POST['parent_id']) ? '' : $_POST['parent_id']).'" />';

$this->endWidget();

?>
