<?php

class RescheduleController extends Controller
{

	/*
	 * overidden as mulitple models effected
	 */
	protected function createSave($model, &$models=array())
	{
		$saved = true;

		// resechedule the task and save its old date in this model
		$task = Task::model()->findByPk($model->task_id);
		$model->old_scheduled = $task->scheduled;
		$task->scheduled = $model->scheduled;
		$saved & parent::createSave($task, $models);
		
		return $saved & parent::createSave($model, $models);
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model,  &$models=array())
	{
		$task = Task::model()->findByPk($model->task_id);
		$task->scheduled = $this->scheduled;

		return parent::updateSave($task, $models);
	}

}
?>