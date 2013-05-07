<?php

/**
 * This is the model class for table "duty".
 *
 * The followings are the available columns in table 'duty':
 * @property string $id
 * @property string $task_id
 * @property integer $duty_type_id
 * @property string $duty_data_id
 * @property integer $responsible
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property DutyData $dutyData
 * @property DutyType $dutyType
 * @property Staff $staff
 * @property Staff $responsible0
 * @property Task $task
 */
class Duty extends ActiveRecord
{
	public $assignedTo;
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	public $description;
	public $searchInCharge;
	public $searchImportance;
	public $generic_id;
	public $updated;
	public $due;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, duty_type_id', 'required'),
			array('duty_type_id, responsible', 'numerical', 'integerOnly'=>true),
			array('task_id, duty_data_id', 'length', 'max'=>10),
			array('updated, generic_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, due, searchImportance, searchInCharge, searchTask, description, updated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'dutyData' => array(self::BELONGS_TO, 'DutyData', 'duty_data_id'),
			'dutyType' => array(self::BELONGS_TO, 'DutyType', 'duty_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'responsible0' => array(self::BELONGS_TO, 'Staff', 'responsible'),
			'task' => array(self::BELONGS_TO,'Task', 'task_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'duty_type_id' => 'Duty/Role/First/Last/Email',
			'description' => 'Duty',
			'responsible' => 'Assigned to',
			'updated' => 'Completed',
			'generic_id' => 'Generic',
			'searchInCharge' => 'Assigned to',
			'searchImportance' => 'Importance',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		// NB: taking first non null of either the staff assigned to duty at project or staff in charge at target duty level
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'dutyType.description AS description',
			'(SELECT `date` FROM working_days WHERE id = (SELECT id - lead_in_days FROM working_days WHERE `date` <= day.scheduled ORDER BY id DESC LIMIT 1)) as due',
			"COALESCE(
				IF(LENGTH(CONCAT_WS('$delimiter',
					responsible.first_name,
					responsible.last_name,
					responsible.email
					))=0, NULL, CONCAT_WS('$delimiter',
					responsible.first_name,
					responsible.last_name,
					responsible.email
					)),
				IF(LENGTH(CONCAT_WS('$delimiter',
					duty_default.first_name,
					duty_default.last_name,
					duty_default.email
					))=0, NULL, CONCAT_WS('$delimiter',
					duty_default.first_name,
					duty_default.last_name,
					duty_default.email
					)),
				CONCAT_WS('$delimiter',
					inCharge.first_name,
					inCharge.last_name,
					inCharge.email
					)
				) AS searchInCharge",
			'dutyData.updated AS updated',
			'taskTypeToDutyType.importance AS searchImportance',
		);

		// where
		$criteria->compare('dutyType.description',$this->description,true);
		$criteria->compare('taskTypeToDutyType.importance',$this->searchImportance,true);
		$this->compositeCriteria(
			$criteria,
			array(
				'duty_default.first_name',
				'duty_default.last_name',
				'duty_default.email',
			), $this->searchInCharge);
		$this->compositeCriteria($criteria,
			array(
				'inCharge.first_name',
				'inCharge.last_name',
				'inCharge.email',
			),
			$this->searchInCharge
		);
		$criteria->compare('updated',Yii::app()->format->toMysqlDateTime($this->updated));
		$criteria->compare('t.task_id',$this->task_id);

		// NB: without this the has_many relations aren't returned and some select columns don't exist
		$criteria->together = true;

		// join
		$criteria->join = '
			JOIN task ON t.task_id = task.id
			JOIN project ON task.project_id = project.id
			JOIN crew ON task.crew_id = crew.id
			JOIN day ON crew.day_id = day.id
			JOIN duty_type dutyType ON t.duty_type_id = dutyType.id
			LEFT JOIN task_type_to_duty_type taskTypeToDutyType
			USING ( task_type_id, duty_type_id )
			LEFT JOIN project_type_to_AuthItem projectTypeToAuthItem ON taskTypeToDutyType.project_type_to_AuthItem_id = projectTypeToAuthItem.id
			LEFT JOIN project_to_project_type_to_AuthItem projectToProjectTypeToAuthItem ON projectTypeToAuthItem.id = projectToProjectTypeToAuthItem.project_type_to_AuthItem_id
			LEFT JOIN AuthAssignment ON projectToProjectTypeToAuthItem.AuthAssignment_id = AuthAssignment.id
			LEFT JOIN staff duty_default ON AuthAssignment.userid = duty_default.id
			LEFT JOIN staff responsible ON t.responsible = responsible.id
		';
		
		// with
		$criteria->with = array(
			'dutyData',
			'dutyData.planning.inCharge',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('description');
        $columns[] = static::linkColumn('searchInCharge', 'Staff', 'assignedTo');
        $columns[] = 'searchImportance';
		$columns[] = 'due:date';
		$columns[] = 'updated:datetime';

		return $columns;
	}

	static function getDisplayAttr()
	{
		return array(
			'dutyType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'description', 'updated', 'searchInCharge', 'searchImportance', 'due');
	}

	public function beforeValidate()
	{
//		if(isset($this->task_type_to_duty_type_id))
//		{
//			$model = TaskTypeToDutyType::model()->findByPk($this->task_type_to_duty_type_id);
//			$this->task_type_id = $model->task_type_id ;
//			$this->duty_type_id = $model->duty_type_id ;
//		}
		
		return parent::beforeValidate();
	}

	public function afterFind() {

		$this->updated = $this->dutyData->updated;

		// get who the duty is assigned to
// TODO: this may be ineffecient - may be better to do a intersecting join on 2 result sets working each way around the circular here instead of back to
// the start i.e. the duty table		
		// if duty not directly assigned to project
/*		$sql = '
			SELECT userid
			FROM duty
			JOIN task_type_to_duty_type ON duty.task_type_to_duty_type_id = task_type_to_duty_type.id
			JOIN project_type_to_AuthItem ON task_type_to_duty_type.project_type_to_AuthItem_id = project_type_to_AuthItem.id
			JOIN project_to_project_type_to_AuthItem ON project_type_to_AuthItem.id = project_to_project_type_to_AuthItem.project_type_to_AuthItem_id
			JOIN project ON project_to_project_type_to_AuthItem.project_id = project.id
			JOIN task ON project.id = task.project_id
			JOIN duty d ON task.id = d.task_id
			JOIN AuthAssignment ON project_to_project_type_to_AuthItem.AuthAssignment_id = AuthAssignment.id
			WHERE duty.id =:id
			AND d.id =:id';
		$id = $this->id;
		$command=Yii::app()->db->createCommand($sql);
		$command->bindParam(":id", $id, PDO::PARAM_STR);
		if(!$this->assignedTo = $command->queryScalar())
		{
			// get who is responsible at the target accummulating level for this duty. Because DutyData is at that desired level it links
			// to correct Planning to get the in_charge
			$this->assignedTo = $this->dutyData->planning->in_charge_id;
		}*/
		
		parent::afterFind();
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		$saved = true;
		$this->dutyData->updated = $this->updated;

		// if we need to update a generic
		if($generic = $this->dutyData->generic)
		{
			// massive assignement
			$generic->attributes=$_POST['Generic'][$generic->id];

			// validate and save
			$saved &= $generic->updateSave($models, array(
				'genericType' => $this->taskTypeToDutyType->dutyType->genericType,
				'params' => array('relationToGenericType'=>'duty->taskTypeToDutyType->dutyType->genericType'),
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
		if(($level = $this->dutyType->level) == Planning::planningLevelTaskInt)
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
			$dutyData->duty_type_id = $this->duty_type_id;
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
			'duty_type_id'=>$this->duty_type_id,
		));

		// if there isn't already a generic item to hold value and there should be
		if(empty($dutyData->generic) && !empty($this->dutyType->generic_type_id))
		{
			// create a new generic item to hold value
			$saved &= Generic::createGeneric($this->dutyType, $models, $generic);
			// associate the new generic to this duty
			$dutyData->generic_id = $generic->id;
			// attempt save
			$saved &= $dutyData->createSave($models);
		}

		// link this Duty to the DutyData
		$this->duty_data_id = $dutyData->id;

		return $saved & parent::createSave($models);
	}

}

?>