<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model));

	if($model->isNewRecord)
	{
		if(isset($model->task_id))
		{
			$form->hiddenField('task_id');
		}
		else
		{
			throw new CHttpException(400, 'No tassk identified, you must get here from the tasks page');
		}

		GenericTaskTypeController::listWidgetRow($model, $form, 'generic_task_type_id');

		$form->textFieldRow('generic_id');
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
