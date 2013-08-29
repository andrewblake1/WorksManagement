<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$form->textFieldRow('description');

	$form->textFieldRow('lead_in_days');

	$form->dropDownListRow('level', Planning::$levels);

	AuthItemController::listWidgetRow($model, $form, 'auth_item_name', array(), array('roles'));

	$form->textAreaRow('comment');
	
	$form->hiddenField('client_id');
	$form->hiddenField('project_template_id');
	
$this->endWidget();

?>