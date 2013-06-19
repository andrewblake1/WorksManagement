<?php

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>'task_id'));

	if($model->isNewRecord)
	{
		UserController::listWidgetRow($model, $form, 'responsible', array(), array(), 'Responsible');
	}
	else
	{
		// if previously saved
		if($model->dutyData->updated)
		{
			$form->textFieldRow(
				'updated',
				Yii::app()->user->checkAccess('system admin') ? array() : array('readonly'=>'readonly'),
				$model->dutyData);
		}
		else
		{
			UserController::listWidgetRow($model->dutyData, $form, 'responsible', array(), array(), 'Assigned to');

			// only allow to be checked if dependencies have been checked
			if(ViewDuty::model()->findAll($incompleteDependencies = $model->incompleteDependencies))
			{
				// display a 3 column grid widget with paging showing dependency step, who is responsible if any, and the due date for it
				Yii::app()->controller->widget('bootstrap.widgets.TbGridView',array(
					'id'=>'dependency-grid',
					'type'=>'striped',
					'dataProvider'=>new CActiveDataProvider('ViewDuty', array('criteria'=>$incompleteDependencies)),
					'columns'=>array(
						'description::Dependent on',
						'derived_assigned_to_name',
						'due',
					),
					'template'=>"{items}\n{pager}",
				));
			}
			else
			{
				$model->dutyData->updated = TRUE;
				$form->checkBoxRow('updated', array(), $model->dutyData);
			}
		}

		if(!empty($model->dutyData->custom_value_id))
		{
			$this->widget('CustomFieldWidget', array(
				'form'=>$form,
				'customValue'=>$model->dutyData->customValue,
				'customField'=>$model->dutyData->dutyStep->customField,
				'relationToCustomField'=>'duty->dutyStep->customField',
			));
		}
	}

$this->endWidget();

?>