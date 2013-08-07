<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$actionToPlant = ActionToPlant::model()->findByPk($model->action_toplant_id);
	$model->action_id = $actionToPlant->action_id;
	DutyStepToCustomFieldController::dependantListWidgetRow($model, $form, 'duty_step_to_custom_field_id', 'DutyStep', 'duty_step_id', array(), array(), null, array('scopeAction'=>array($model->action_id)));

	$form->textFieldRow('compare');

$this->endWidget();

?>