<?php
class DutyCustomFieldWidgets extends CustomFieldWidgets
{
	protected function afterCustom($toCustomField, $model)
    {
		echo CHtml::openTag('div', array('id'=>'actionToLabourResourceBranch_' . $toCustomField->id));

		// add related labour resources
		foreach($toCustomField->actionToLabourResourceBranches as $actionToLabourResourceBranch)
		{
			$taskToLabourResource = TaskToLabourResource::model()->findByAttributes(array(
				'task_id'=>$model->task_id,
				$actionToLabourResourceBranch->actionToLabourResource->labour_resource_id,
			));
			$taskToLabourResourceController = new TaskToLabourResourceController;
		}
				
		echo CHtml::closeTag('div', array('id'=>'CustomFields'));
	}
}
?>
