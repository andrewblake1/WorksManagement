<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));
	
	TaskTemplateToLabourResourceController::listWidgetRow($model, $form, 'child_id', array(), array('scopeTaskTemplate'=>array($model->parent_id, $model->parent->task_template_id, $model->parent->mode_id)));

$this->endWidget();

?>