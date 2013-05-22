<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	// get the parent model
	DutyStepController::listWidgetRow($model, $form, 'child_duty_step_id');

$this->endWidget();

?>