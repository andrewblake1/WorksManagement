<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	$actionToLabourResource = ActionToLabourResource::model()->findByPk($model->action_to_labour_resource_id);
	$model->action_id = $actionToLabourResource->action_id;
	DutyStepToCustomFieldController::dependantListWidgetRow($model, $form, 'duty_step_to_custom_field_id', 'DutyStep', 'duty_step_id', array(), array(), null, array('scopeAction'=>array($model->action_id)));

	$form->textFieldRow('compare');

	$form->hiddenField('client_id');
	$form->hiddenField('project_template_id');

$this->endWidget();

?>