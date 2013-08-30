<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	CustomFieldController::listWidgetRow($model, $form, 'custom_field_id');

	$form->textFieldRow('label_override');

	$form->hiddenField('client_id');
	$form->hiddenField('project_template_id');
	$form->hiddenField('action_id');

$this->endWidget();

?>