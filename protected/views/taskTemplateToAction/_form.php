<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	ActionController::listWidgetRow($model, $form, 'action_id', array(), array('scopeTaskTemplate'=>array('taskTemplateId'=>$model->task_template_id)));
	
	$form->dropDownListRow('importance', $model->importanceLabels);

$this->endWidget();

?>
