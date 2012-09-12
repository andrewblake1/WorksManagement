<?php

class CrewController extends Controller
{
	/*
	 * overidden as mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		// need to insert a row into the schedule nested set model so that the id can be used here
		
		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$schedule = new Schedule;
		$schedule->name = $model->scheduleName;
		if($saved = $schedule->appendTo(Schedule::model()->findByPk($model->day_id)))
		{
			$model->id = $schedule->id;
			$saved = parent::createSave($model, $models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $schedule;
		
		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model,  &$models=array())
	{
		// get the schedule model
		$schedule = Schedule::model()->findByPk($model->id);
		$schedule->name = $model->scheduleName;
		// atempt save
		$saved = $schedule->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $schedule;
		
		return $saved & parent::createSave($model, $models);
	}

}

?>