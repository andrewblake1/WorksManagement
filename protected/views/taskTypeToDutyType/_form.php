<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');
	
	ProjectTypeToAuthItemController::listWidgetRow($model, $form, 'project_type_to_AuthItem_id', array(), array(
		'scopeTaskTypeToDutyType'=>array($model->task_type_id),
	));

	$form->dropDownListRow('importance', $model->importanceLabels);

$this->endWidget();

?>
