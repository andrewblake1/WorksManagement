<?php

$form=$this->beginWidget('WMTbActiveForm', array(
	'model'=>$model,
	'action'=>empty($action) ? null : $action, 
	'parent_fk'=>$parent_fk,
));

	$form->textFieldRow('name');

	ProjectTemplateController::listWidgetRow($model, $form, 'project_template_id', array(), array('scopeClient'=>array($model->client_id)));
	
	// if adding to another node - as opposed to creating a new root
	echo '<input type="hidden" name="parent_id" value="'.(empty($_POST['parent_id']) ? '' : $_POST['parent_id']).'" />';

$this->endWidget();

?>