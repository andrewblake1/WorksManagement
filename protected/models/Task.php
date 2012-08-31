<?php

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property string $id
 * @property string $description
 * @property string $project_id
 * @property integer $task_type_id
 * @property integer $in_charge_id
 * @property string $planned
 * @property string $scheduled
 * @property string $earliest
 * @property string $preferred
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property MaterialToTask[] $materialToTasks
 * @property Reschedule[] $reschedules
 * @property Reschedule[] $reschedules1
 * @property Project $project
 * @property Staff $staff
 * @property TaskType $taskType
 * @property Staff $inCharge
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 * @property TaskToPurchaseOrder[] $taskToPurchaseOrders
 * @property TaskToResourceType[] $taskToResourceTypes
 * @property TaskType[] $taskTypes
 */
class Task extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchInCharge;
	public $searchProject;
	public $searchTaskType;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, project_id, task_type_id, in_charge_id, staff_id', 'required'),
			array('task_type_id, in_charge_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_id', 'length', 'max'=>10),
			array('planned, scheduled, earliest, preferred', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('searchInCharge, searchProject, searchTaskType, searchStaff, id, description, project_id, planned, scheduled, earliest, preferred', 'safe', 'on'=>'search'),
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
			'duties' => array(self::HAS_MANY, 'Duty', 'task_id'),
			'materialToTasks' => array(self::HAS_MANY, 'MaterialToTask', 'task_id'),
			'reschedules' => array(self::HAS_MANY, 'Reschedule', 'task_id'),
			'reschedules1' => array(self::HAS_MANY, 'Reschedule', 'new_task_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'inCharge' => array(self::BELONGS_TO, 'Staff', 'in_charge_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'task_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'task_id'),
			'taskToPurchaseOrders' => array(self::HAS_MANY, 'TaskToPurchaseOrder', 'task_id'),
			'taskToResourceTypes' => array(self::HAS_MANY, 'TaskToResourceType', 'task_id'),
			'taskTypes' => array(self::HAS_MANY, 'TaskType', 'template_task_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Task',
			'in_charge_id' => 'In charge, First/Last/Email',
			'searchInCharge' => 'In charge, First/Last/Email',
			'project_id' => 'Project',
			'searchProject' => 'Project',
			'task_type_id' => 'Task type',
			'searchTaskType' => 'Task type',
			'planned' => 'Planned',
			'scheduled' => 'Scheduled',
			'earliest' => 'Earliest',
			'preferred' => 'Preferred',

		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			't.in_charge_id',
			't.task_type_id',
			't.description',
			't.planned',
			't.scheduled',
			't.earliest',
			't.preferred',
			"CONCAT_WS('$delimiter',
				inCharge.first_name,
				inCharge.last_name,
				inCharge.email
				) AS searchInCharge",
			"CONCAT_WS('$delimiter',
				taskType.description
				) AS searchTaskType",
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.planned',Yii::app()->format->toMysqlDate($this->planned));
		$criteria->compare('t.scheduled',Yii::app()->format->toMysqlDate($this->scheduled));
		$criteria->compare('t.earliest',Yii::app()->format->toMysqlDate($this->earliest));
		$criteria->compare('t.preferred',Yii::app()->format->toMysqlDate($this->preferred));
		$this->compositeCriteria($criteria,
			array(
				'inCharge.first_name',
				'inCharge.last_name',
				'inCharge.email',
			),
			$this->searchInCharge
		);
		$this->compositeCriteria($criteria,
			array(
				'taskType.description',
			),
			$this->searchTaskType
		);
		$criteria->compare('t.project_id',$this->project_id);
		
		// join
		$criteria->with = array(
			'inCharge',
			'project',
			'taskType',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
        $columns[] = static::linkColumn('searchInCharge', 'Staff', 'in_charge_id');
        $columns[] = static::linkColumn('searchTaskType', 'TaskType', 'task_type_id');
		$columns[] = 'planned';
		$columns[] = 'scheduled';
		$columns[] = 'earliest';
		$columns[] = 'preferred';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchInCharge', 'searchProject', 'searchTaskType');
	}

}

?>