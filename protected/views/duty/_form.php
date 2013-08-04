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
// may need access control here
			UserController::listWidgetRow($model->dutyData, $form, 'responsible', array(), array(), 'Assigned to');
			$form->dropDownListRow('level', Planning::$levels, array(), $model->dutyData);

			// only allow to be checked if dependencies have been checked
			if(Duty::model()->findAll($incompleteDependencies = $model->incompleteDependencies))
			{
				// display a 3 column grid widget with paging showing dependency step, who is responsible if any, and the due date for it
				Yii::app()->controller->widget('bootstrap.widgets.TbGridView',array(
					'id'=>'dependency-grid',
					'type'=>'striped',
					'dataProvider'=>new CActiveDataProvider('Duty', array('criteria'=>$incompleteDependencies)),
					'columns'=>array(
						'description::Dependent on',
						'derived_assigned_to_name',
						'due',
					),
					'template'=>"{items}\n{pager}",
				));
			}
			elseif(!$model->dutyData->updated)
			{
// may need access control here
				$form->checkBoxRow('updated', array(), $model->dutyData);
			}
		}

		$this->widget('CustomFieldWidgets',array(
			'model'=>$model,
			'form'=>$form,
			'relationModelToCustomFieldModelTemplate'=>'dutyDataToDutyStepToCustomField',
			'relationModelToCustomFieldModelTemplates'=>'dutyData->dutyDataToDutyStepToCustomFields',
			'relationCustomFieldModelTemplate'=>'dutyStepToCustomField',
			'relation_category'=>'customFieldDutyStepCategory',
			'categoryModelName'=>'CustomFieldDutyStepCategory',
		));
		
		// need to show previous steps custom fields on duty form as disabled
		$this->previousStepsCustomFields($model, $form);
	}

$this->endWidget();

?>