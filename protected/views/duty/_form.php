<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

	if($model->isNewRecord)
	{
		DutyTypeController::listWidgetRow($model, $form, 'duty_type_id');
		StaffController::listWidgetRow($model, $form, 'responsible', array(), array(), 'Responsible');
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
			$form->checkBoxRow('updated');
			
			// allow system admin and original creator of duty to be able to alter who it is assigned to
			if($model->staff_id == Yii::app()->user->id || Yii::app()->user->checkAccess('system admin'))
			{
				StaffController::listWidgetRow($model, $form, 'responsible', array(), array(), 'Assigned to');
			}
		}

		if(!empty($model->dutyData->generic_id))
		{
			$this->widget('GenericWidget', array(
				'form'=>$form,
				'generic'=>$model->dutyData->generic,
				'genericType'=>$model->dutyType->genericType,
				'relationToGenericType'=>'duty->dutyType->genericType',
			));
		}
	}

$this->endWidget();

?>
