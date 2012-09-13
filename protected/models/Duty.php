<?php

/**
 * This is the model class for table "duty".
 *
 * The followings are the available columns in table 'duty':
 * @property string $id
 * @property string $task_id
 * @property integer $task_type_id
 * @property integer $duty_type_id
 * @property integer $task_type_to_duty_type_id
 * @property string $duty_data_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property TaskTypeToDutyType $taskType
 * @property Staff $staff
 * @property TaskTypeToDutyType $taskTypeToDutyType
 * @property DutyData $dutyType
 * @property DutyData $dutyData
 */
class Duty extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	public $description;
	public $searchInCharge;
	public $generic_id;
	public $updated;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'duty';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, task_type_id, duty_type_id, task_type_to_duty_type_id, duty_data_id, staff_id', 'required'),
			array('task_type_id, duty_type_id, task_type_to_duty_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id, duty_data_id', 'length', 'max'=>10),
			array('updated, generic_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, searchInCharge, searchTask, description, updated, searchStaff', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskTypeToDutyType', 'task_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskTypeToDutyType' => array(self::BELONGS_TO, 'TaskTypeToDutyType', 'task_type_to_duty_type_id'),
			'dutyType' => array(self::BELONGS_TO, 'DutyData', 'duty_type_id'),
			'dutyData' => array(self::BELONGS_TO, 'DutyData', 'duty_data_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Duty',
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'task_type_id' => 'Task type',
			'task_type_to_duty_type_id' => 'Duty/Role/First/Last/Email',
			'description' => 'Duty',
			'updated' => 'Completed',
			'generic_id' => 'Generic',
			'searchInCharge' => 'In charge',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		// NB: taking first non null of either the staff assigned to duty at project or staff in charge at target duty level
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.task_type_to_duty_type_id',
			'dutyType.description AS description',
			"COALESCE(
				IF(LENGTH(CONCAT_WS('$delimiter',
					user.first_name,
					user.last_name,
					user.email
					))=0, NULL, CONCAT_WS('$delimiter',
					user.first_name,
					user.last_name,
					user.email
					)),
				CONCAT_WS('$delimiter',
					inCharge.first_name,
					inCharge.last_name,
					inCharge.email
					)
				) AS searchInCharge",
			'dutyData.updated AS updated',
		);

		// where
		$criteria->compare('dutyType.description',$this->description,true);
		$this->compositeCriteria(
			$criteria,
			array(
				'user.first_name',
				'user.last_name',
				'user.email',
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

/*		if(!$assignedTo = $model->task->project->projectToProjectTypeToAuthItems->authAssignment->userid)
		{
			// get who is responsible at the target accummulating level for this duty. Because DutyData is at that desired level it links
			// to correct Schedule to get the in_charge
			$assignedTo = $model->dutyData->schedule->in_charge_id;
		}
		*/
		
		// join
		$criteria->with = array(
			'dutyData',
			'dutyData.schedule.inCharge',
			'task.project.projectToProjectTypeToAuthItems.authAssignment',
			'task.project.projectToProjectTypeToAuthItems.authAssignment.user',
			'taskTypeToDutyType.dutyType',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('description', 'TaskTypeToDutyType', 'task_type_to_duty_type_id');
        $columns[] = static::linkColumn('searchInCharge', 'Staff', 'dutyData->schedule->in_charge_id');
		$columns[] = 'updated:datetime';
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array('taskTypeToDutyType->dutyType->description');
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'description', 'updated');
	}

	public function beforeValidate()
	{
		if(isset($this->task_type_to_duty_type_id))
		{
			$model = TaskTypeToDutyType::model()->findByPk($this->task_type_to_duty_type_id);
			$this->task_type_id = $model->task_type_id ;
		}
		
		return parent::beforeValidate();
	}

	public function beforeSave()
	{
		// if the updated attribute was null but is now being set
		if($this->updated == 1 && $this->dutyData->getOldAttributeValue('updated') == null)
		{
			// set to current datetime
			$this->dutyData->updated = date('Y-m-d H:i:s');
		}
		// system admin clear
		elseif(empty($this->updated) && Yii::app()->user->checkAccess('system admin'))
		{
			// clear
			$this->dutyData->updated = null;
		}
		
		return parent::beforeSave();
	}

	public function afterFind() {
		$this->updated = $this->dutyData->updated;
		
		parent::afterFind();
	}
}

?>