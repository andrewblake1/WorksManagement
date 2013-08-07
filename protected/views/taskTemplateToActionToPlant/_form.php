<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ActionToPlantController::listWidgetRow($model, $form, 'action_to_plant_id');

	$form->textFieldRow('quantity');

	$form->textFieldRow('duration');

$this->endWidget();

?>