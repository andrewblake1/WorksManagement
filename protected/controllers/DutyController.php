<?php

class DutyController extends Controller
{
	/**
	 * Specifies the access control rules.
	 * NB: need to override this to open up so can shift access control into actionUpdate method to pass params for bizrule
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('admin','index','view', 'update'),
				'roles'=>array($this->modelName.'Read'),
			),
			array('allow',
				'actions'=>array('create','delete','autocomplete'),
				'roles'=>array($this->modelName),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	// special handling of update for duties
	public function actionUpdate($id)
	{
		// get duty
		$model = Duty::model()->findByPk($id);

		// system admin
		if(Yii::app()->user->checkAccess('system admin'))
		{
			// is the only one allowed to alter once ticked off as complete
			parent::actionUpdate($id);
		}
		// other users with full Duty access or has DutyUpdate permission - has to be assigned to this duty
		elseif(Yii::app()->user->checkAccess('Duty') || Yii::app()->user->checkAccess('DutyUpdate', array('assignedTo'=>$model->assignedTo)))
		{
			// can only update if not completed
			if(!empty($model->updated))
			{
				parent::actionUpdate($id);
			}
			// otherwise can view
			{
				$this->actionView($id);
			}
		}
		// otherwise doesn't have permission to be here
		else
		{
			throw new CHttpException(403,'You do not have permission to view this duty.');
		}
	}

	/*
	 * overidden as mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		// not using this so able to make this static as needed in TaskController
		return static::createSaveStatic($model, $models);
	}

	static function createSaveStatic($model, &$models=array())
	{
		$saved = true;
		
		// this is repeated in before validate but variables arn't held so need recalc here
		$taskTypeToDutyType = TaskTypeToDutyType::model()->findByPk($model->task_type_to_duty_type_id);
		$model->task_type_id = $taskTypeToDutyType->task_type_id ;
		$model->duty_type_id = $taskTypeToDutyType->duty_type_id ;
		
		// ensure existance of a related DutyData. First get the desired planning id which is the desired ancestor of task
		// if this is task level
		if(($level = $model->taskTypeToDutyType->dutyType->level) == Planning::planningLevelTaskInt)
		{
			$planning_id = $model->task_id;
		}
		else
		{
			// get the desired ansestor
			$planning = Planning::model()->findByPk($model->task_id);

			while($planning = $planning->parent)
			{
				if($planning->level == $level)
				{
					break;
				}
			}
			if(empty($planning))
			{
				throw new Exception();
			}

			$planning_id = $planning->id;
		}
		// try insert and catch and dump any error - will ensure existence
		try
		{
			$dutyData = new DutyData;
			$dutyData->planning_id = $planning_id;
			$dutyData->duty_type_id = $model->duty_type_id;
			$dutyData->level = $level;
			// NB not recording return here as might fail deliberately if already exists - though will go to catch
			$dutyData->dbCallback('save');
		}
		catch (CDbException $e)
		{
			// dump

		}
		// retrieve the DutyData
		$dutyData = DutyData::model()->findByAttributes(array(
			'planning_id'=>$planning_id,
			'duty_type_id'=>$model->duty_type_id,
		));

		// if there isn't already a generic item to hold value and there should be
		if(empty($dutyData->generic) && !empty($model->taskTypeToDutyType->dutyType->generic_type_id))
		{
			// create a new generic item to hold value
			$saved &= Generic::createGeneric($model->taskTypeToDutyType->dutyType->genericType, $models, $generic);
			// associate the new generic to this duty
			$dutyData->generic_id = $generic->id;
			// attempt save
			$saved &= parent::createSave($dutyData, $models);
		}

		// link this Duty to the DutyData
		$model->duty_data_id = $dutyData->id;

		return $saved & parent::createSave($model, $models);
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model,  &$models=array())
	{
		$saved = true;
		$model->dutyData->updated = $model->updated;

		// if we need to update a generic
		if($generic = $model->dutyData->generic)
		{
			// massive assignement
			$generic->attributes=$_POST['Generic'][$generic->id];

			// set Generic custom validators as per the associated generic type
			$generic->setCustomValidators($model->taskTypeToDutyType->dutyType->genericType,
				array(
					'relationToGenericType'=>'duty->taskTypeToDutyType->dutyType->genericType',
				)
			);

			$saved &= parent::updateSave($generic, $models);
		}

		// attempt save of related DutyData
		$saved &= parent::updateSave($model->dutyData, $models);
		
		return $saved & parent::updateSave($model, $models);
	}

	/*
	 * overidden as mulitple models
	 */
	protected function actionAfterDelete($model)
	{
		// stop orphans in DutyData and Generic

		$criteria=new CDbCriteria;
		$criteria->compare('duty_data_id', $model->duty_data_id);

		// if DutyData now orphaned
		if(Duty::model()->count($criteria) == 0)
		{
			// get the DutyData model
			$dutyData = DutyData::model()->findByPk($model->duty_data_id);
			
			// get generic id before removing DutyData
			$generic_id = $dutyData->generic_id;
			
			// NB: must delete dutyData first as looks up generic
			$dutyData->delete();
			
			// if there is a generic
			if($generic_id)
			{
				// delete the generic
				Generic::model()->deleteByPk($generic_id);
			}

		}
	}

}

?>