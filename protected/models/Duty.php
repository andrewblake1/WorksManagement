<?php

/**
 * This is the model class for table "tbl_duty".
 *
 * The followings are the available columns in table 'tbl_duty':
 * @property string $id
 * @property string $task_id
 * @property string $duty_data_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property DutyData $dutyData
 */
class Duty extends ActiveRecord
{
	public $derived_assigned_to_id;
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $description;
	public $derived_assigned_to_name;
	public $derived_importance;
	public $custom_value_id;
	public $updated;
	public $due;
	
	public $duty_step_id;
	public $responsible;
	public $task_to_action_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('task_id, duty_step_id', 'required'),
			array('duty_step_id', 'numerical', 'integerOnly'=>true),
			array('task_id, duty_data_id', 'length', 'max'=>10),
			array('updated, custom_value_id', 'safe'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'dutyData' => array(self::BELONGS_TO, 'DutyData', 'duty_data_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'duty_step_id' => 'Duty/Role/First/Last/Email',
			'description' => 'Duty',
			'responsible' => 'Assigned to',
			'updated' => 'Completed',
			'custom_value_id' => 'Custom value',
			'derived_assigned_to_name' => 'Assigned to',
			'derived_importance' => 'Importance',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		// NB: taking first non null of either the user assigned to duty at project or user in charge at target duty level
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			'*',
		);

		// where
		$criteria->compare('description',$this->description,true);
		$criteria->compare('derived_importance',$this->derived_importance,true);
		$criteria->compare('updated',Yii::app()->format->toMysqlDateTime($this->updated));
		$criteria->compare('t.task_id',$this->task_id);
		
		// NB: without this the has_many relations aren't returned and some select columns don't exist
		$criteria->together = true;
		
		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('description');
        $columns[] = static::linkColumn('derived_assigned_to_name', 'User', 'derived_assigned_to_id');
        $columns[] = 'derived_importance';
		$columns[] = 'due:date';
		$columns[] = 'updated:datetime';

		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'dutyData->dutyStep->description',
		);
	}

	public function afterFind() {

		$this->updated = $this->dutyData->updated;
		if(!$this->derived_assigned_to_id)
		{
			$this->derived_assigned_to_id = 1;
		}
		
		parent::afterFind();
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		$saved = true;
		$this->dutyData->updated = $this->updated;

		// if we need to update a customValue
		if($customValue = $this->dutyData->customValue)
		{
			// massive assignement
			$customValue->attributes=$_POST['CustomValue'][$customValue->id];

			// validate and save
			$saved &= $customValue->updateSave($models, array(
				'customField' => $this->dutyStepDependency->childDutyStep->customField,
				'params' => array('relationToCustomField'=>'dutyStepDependency->childDutyStep->customField'),
			));
		}

		// attempt save of related DutyData
		$saved &= $this->dutyData->updateSave($models);
		
		return $saved & parent::updateSave($models);
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		$saved = true;

		// ensure existance of a related DutyData. First get the desired planning id which is the desired ancestor of task
		// if this is task level
		$dutyStep = DutyStep::model()->findByPk($this->duty_step_id);

		if(($level = $dutyStep->level) == Planning::planningLevelTaskInt)
		{
			$planning_id = $this->task_id;
		}
		else
		{
			// get the desired ansestor
			$planning = Planning::model()->findByPk($this->task_id);

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
			$dutyData->duty_step_id = $dutyStep->id;
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
			'duty_step_id'=>$dutyStep->id,
		));

		// if there isn't already a customValue item to hold value and there should be
		if(empty($dutyData->customValue) && !empty($dutyStep->custom_field_id))
		{
			// create a new customValue item to hold value
			$saved &= CustomValue::createCustomField($dutyStep, $models, $customValue);
			// associate the new customValue to this duty
			$dutyData->custom_value_id = $customValue->id;
			// attempt save
			$saved &= $dutyData->createSave($models);
		}

		// link this Duty to the DutyData
		$this->duty_data_id = $dutyData->id;

		return $saved & parent::createSave($models);
	}

	public function getIncompleteDependencies()
	{
		// get any incomplete children
		$criteria = new DbCriteria;
		
		// join to get depencies then need to join back to get updated value on the children
		$criteria->join="
			JOIN tbl_duty_data dutyData ON t.duty_data_id = dutyData.id
			JOIN tbl_duty_step_dependency dutyStepDependency ON dutyData.duty_step_id = dutyStepDependency.parent_duty_step_id
			
			JOIN tbl_duty_data dutyDataDependency ON dutyStepDependency.child_duty_step_id = dutyDataDependency.duty_step_id
		";
		
		$criteria->compareNull('dutyDataDependency.updated');
		
		return static::model()->findAll($criteria);
	}
	
	/* 
	 * factory method for creating Duties based on actionid and task id
	 */
	public static function addDuties($actionId, $taskId, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// get the action
		$action = Action::model()->findByPk($actionId);
	
		// loop thru steps of the Action
		foreach($action->dutySteps as $dutyStep)
		{
			// create a new duty
			$duty = new Duty();
			// copy any useful attributes from
			$duty->task_id = $taskId;
			$duty->duty_step_id = $dutyStep->id;
			$saved &= $duty->createSave($models);
		}
		
		return $saved;
	}
	
	public static function getParentForeignKey($referencesModel, $foreignKeys = array()) {
		// No actual TaskToAdmin - actual parent is task
		if($referencesModel == 'TaskToAction')
		{
			return 'task_to_action_id';
		}
		
		return parent::getParentForeignKey($referencesModel, $foreignKeys);
	}
	
	/*
	 * Need to override becuase parent is TaskToAction which doesn't exist
	 */
	public function assertFromParent($modelName = null)
	{
		if($this->task_id)
		{
			$taskId = $this->task_id;
			$task = $this->task;
		}
		else
		{
			$criteria = new DbCriteria;
			
			$criteria->compare('dutyStep.action_id', $this->task_to_action_id);
			
			$criteria->with=array(
				'dutyData',
				'dutyData.dutyStep',
			);
			$task = Duty::model()->find($criteria);
			$taskId = $task->id;
		}

		// store the primary key for the model
		Controller::setUpdateId($taskId, 'TaskToAction');
		// ensure that that at least the parents primary key is set for the admin view
		Controller::setAdminParam('task_id', $taskId, 'Duty');

		// assert the task
		return $task->assertFromParent();
	}

}

?>