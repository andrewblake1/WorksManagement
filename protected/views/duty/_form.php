<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	if($model->isNewRecord)
	{
//		DutyStepController::listWidgetRow($model, $form, 'duty_step_id');
		UserController::listWidgetRow($model, $form, 'responsible', array(), array(), 'Responsible');
	}
	else
	{
		// if previously saved
		if($model->updated)
		{
			$form->textFieldRow('updated', Yii::app()->user->checkAccess('system admin') ? array() : array('readonly'=>'readonly'));
		}
		else
		{
			// only allow to be checked if dependencies have been checked
			if($dependencyModels = $model->incompleteDependencies)
			{
				// make this a widget or part of controller returning grid with links to the duties if allowed
				foreach($dependencyModels AS $dependencyModel);
				{
					
				}
			}
			else
			{
				$form->checkBoxRow('updated');
			}
			
			// allow system admin and original creator of duty to be able to alter who it is assigned to
			if($model->updated_by == Yii::app()->user->id || Yii::app()->user->checkAccess('system admin'))
			{
				UserController::listWidgetRow($model, $form, 'responsible', array(), array(), 'Assigned to');
			}
		}

		if(!empty($model->dutyData->custom_value_id))
		{
			$this->widget('CustomFieldWidget', array(
				'form'=>$form,
				'customValue'=>$model->dutyData->customValue,
				'customField'=>$model->dutyStep->customField,
				'relationToCustomField'=>'duty->dutyStep->customField',
			));
		}
	}

$this->endWidget();

?>
