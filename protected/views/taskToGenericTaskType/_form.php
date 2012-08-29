<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if($model->isNewRecord)
	{
		GenericTaskTypeController::listWidgetRow($model, $form, 'generic_task_type_id');
//		$form->textFieldRow('generic_id');
	}
	else
	{
		$this->widget('GenericWidget', array(
			'form'=>$form,
			'relation_modelToGenericModelType'=>'taskToGenericTaskType',
			'toGenericType'=>$model,
			'relation_genericModelType'=>'genericTaskType',
		));
	}

$this->endWidget();

?>
