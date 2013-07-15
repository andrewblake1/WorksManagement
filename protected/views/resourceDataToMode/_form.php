<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>'resource_data_id'));

	if($model->isNewRecord)
	{
		$taskToResource = TaskToResource::model()->findByPk($_GET['task_to_resource_id']);
		$model->resource_data_id = $taskToResource->resource_data_id;
		// this needed so can return to correct place after creation
		echo CHtml::hiddenField('task_to_resource_id', $_GET['task_to_resource_id']);
	}

	ModeController::listWidgetRow($model, $form, 'mode_id');

$this->endWidget();

?>