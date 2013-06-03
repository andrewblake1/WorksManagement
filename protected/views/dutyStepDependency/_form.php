<?php
if($this->action->id != 'update')
{
	$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk, 'action'=>$this->createUrl('create', $_GET)));

		// hidden field to parent duty step
		if(!empty($_GET['duty_step_dependency_ids']))
		{
			$dutyStepDependency = DutyStepDependency::model()->findByPk(array_pop($_GET['duty_step_dependency_ids']));
			$model->parent_duty_step_id = $dutyStepDependency->child_duty_step_id;
			$form->hiddenField('parent_duty_step_id');
		}

		// get the parent model
		$dutyStep = DutyStep::model()->findByPk(static::getUpdateId('DutyStep'));
		DutyStepController::listWidgetRow($model, $form, 'child_duty_step_id', array(),
			array('scopeAction'=>array($model->action_id)));
}
else
{
	$form=$this->beginWidget('WMTbActiveForm', array('model'=>$model, 'parent_fk'=>$parent_fk));

		// hidden field to parent duty step
		if(!empty($_GET['duty_step_dependency_ids']))
		{
			$dutyStepDependency = DutyStepDependency::model()->findByPk(array_pop($_GET['duty_step_dependency_ids']));
			$form->hiddenField('child_duty_step_id');
		}

		// get the parent model
		$dutyStep = DutyStep::model()->findByPk(static::getUpdateId('DutyStep'));
		DutyStepController::listWidgetRow($model, $form, 'parent_duty_step_id', array(),
			array('scopeAction'=>array($model->action_id)));
}

$this->endWidget();

?>