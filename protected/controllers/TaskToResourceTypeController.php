<?php

class TaskToResourceTypeController extends Controller
{

	/*
	 * overidden as mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		// not using this so able to make this static as needed in TaskController
		return createSaveStatic($model, $models);
	}

	private static function insertResourceData(&$model)
	{
		if($model->level === null)
		{
			$model->level = Schedule::scheduleLevelTaskInt;
		}
// TODO: a lot of this repeated in resource controller - abstract out - perhaps into ScheduleController static function
		// ensure existance of a related ResourceData. First get the desired schedule id which is the desired ancestor of task
		// if this is task level
		if(($level = $model->level) == Schedule::scheduleLevelTaskInt)
		{
			$schedule_id = $model->task_id;
		}
		else
		{
			// get the desired ansestor
			$schedule = Schedule::model()->findByPk($model->task_id);

			while($schedule = $schedule->parent)
			{
				if($schedule->level == $level)
				{
					break;
				}
			}
			if(empty($schedule))
			{
				throw new Exception();
			}

			$schedule_id = $schedule->id;
		}
		// try insert and catch and dump any error - will ensure existence
		try
		{
			$resourceData = new ResourceData;
			$resourceData->schedule_id = $schedule_id;
			$resourceData->resource_type_id = $model->resource_type_id;
			$resourceData->level = $level;
			$resourceData->quantity = $model->quantity;
			$resourceData->hours = $model->hours;
			$resourceData->start = $model->start;
			// NB not recording return here as might fail deliberately if already exists - though will go to catch
			$resourceData->dbCallback('save');
		}
		catch (CDbException $e)
		{
			// dump

		}
		// retrieve the ResourceData
		$resourceData = ResourceData::model()->findByAttributes(array(
			'schedule_id'=>$schedule_id,
			'resource_type_id'=>$model->resource_type_id,
		));

		// link this Resource to the ResourceData
		$model->resource_data_id = $resourceData->id;
	}

	static function createSaveStatic($model, &$models=array())
	{
		$saved = true;
		
		self::insertResourceData($model);
	
		return $saved & parent::createSave($model, $models);
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model, &$models=array())
	{
		$saved = true;
		
		// ensure the related items are set
		$model->beforeSave();
		$oldResourceDataId = $model->resourceData->id;


		// ensure the ResourceData has correct level by inserting a new one if necassary or linking to correct
		self::insertResourceData($model);
		
		// clear orphan if any
		self::removeResourceDataOrphan($oldResourceDataId);

		// attempt save of related ResourceData
//		$saved &= parent::updateSave($model->resourceData, $models);
		
		return $saved & parent::updateSave($model, $models);
	}

	/*
	 * overidden as mulitple models
	 */
	private static function removeResourceDataOrphan($id)
	{
		// stop orphans in ResourceData and Generic

		$criteria=new CDbCriteria;
		$criteria->compare('resource_data_id', $id);

		// if ResourceData now orphaned
		if(TaskToResourceType::model()->count($criteria) == 0)
		{
			// delete
			ResourceData::model()->deleteByPk($id);
		}
	}

	/*
	 * overidden as mulitple models
	 */
	protected function actionAfterDelete($model)
	{
		self::removeResourceDataOrphan($model->resource_data_id);
	}

}

?>