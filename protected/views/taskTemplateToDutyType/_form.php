<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');
	
	ProjectTemplateToAuthItemController::listWidgetRow($model, $form, 'project_template_to_auth_item_id', array(), array(
		'scopeTaskTemplateToDutyType'=>array($model->task_template_id),
	));

	$form->dropDownListRow('importance', $model->importanceLabels);

$this->endWidget();

?>
