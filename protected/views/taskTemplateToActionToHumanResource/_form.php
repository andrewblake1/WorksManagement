<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ActionToHumanResourceController::listWidgetRow($model, $form, 'action_to_human_resource_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('duration');

$this->endWidget();

?>