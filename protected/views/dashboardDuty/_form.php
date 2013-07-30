<?php
// only show form for updating - i.e. no new form here
if(!$model->isNewRecord)
{

$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>'task_id'));

	// only allow to be checked if dependencies have been completed
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

		$showCustom = FALSE;
	}
	else
	{
		$model->dutyData->updated = TRUE;
		$form->hiddenField('updated', array(), $model->dutyData);
		$model->updateButtonText = 'Complete';
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

$this->endWidget();
}
?>