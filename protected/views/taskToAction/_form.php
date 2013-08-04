<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	// need to get task template id
	$task = Task::model()->findByPk($model->task_id);
	ActionController::listWidgetRow($model, $form, 'action_id', array(), array('scopeTaskTemplate'=>array('taskTemplateId'=>$task->task_template_id, $task->mode_id)));

$this->endWidget();

?>
