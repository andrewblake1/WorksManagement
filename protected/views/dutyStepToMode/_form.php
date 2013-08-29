<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ModeController::listWidgetRow($model, $form, 'mode_id');

	$form->hiddenField('client_id');
	$form->hiddenField('project_template_id');
	$form->hiddenField('action_id');

$this->endWidget();

?>