<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$actionToHumanResource = ActionToHumanResource::model()->findByPk($model->action_to_human_resource_id);
	$model->action_id = $actionToHumanResource->action_id;
	DutyStepToCustomFieldController::dependantListWidgetRow($model, $form, 'duty_step_to_custom_field_id', 'DutyStep', 'duty_step_id', array(), array(), null, array('scopeAction'=>array($model->action_id)));

$this->endWidget();

?>