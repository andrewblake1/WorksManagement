<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ActionToLabourResourceController::listWidgetRow($model, $form, 'action_to_labour_resource_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('duration');

$this->endWidget();

?>