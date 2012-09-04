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
 * @property integer $staff_id
 * @property integer $preferred_mon
 * @property integer $preferred_tue
 * @property integer $preferred_wed
 * @property integer $preferred_thu
 * @property integer $preferred_fri
 * @property integer $preferred_sat
 * @property integer $preferred_sun
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property MaterialToTask[] $materialToTasks
 * @property Reschedule[] $reschedules
 * @property Project $project
 * @property Staff $staff
 * @property TaskType $taskType
 * @property Staff $inCharge
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 * @property TaskToPurchaseOrder[] $taskToPurchaseOrders
 * @property TaskToResourceType[] $taskToResourceTypes
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
	public $searchEarliest;
	/**
	 * inline checkbox property 
	 */
	public $preferred = array();
	
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
			array('planned, scheduled, preferred', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('searchInCharge, searchEarliest, searchProject, searchTaskType, searchStaff, id, description, project_id, planned, scheduled, preferred_mon, preferred_tue, preferred_wed, preferred_thu, preferred_fri, preferred_sat, preferred_sun', 'safe', 'on'=>'search'),
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
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'inCharge' => array(self::BELONGS_TO, 'Staff', 'in_charge_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'task_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'task_id'),
			'taskToPurchaseOrders' => array(self::HAS_MANY, 'TaskToPurchaseOrder', 'task_id'),
			'taskToResourceTypes' => array(self::HAS_MANY, 'TaskToResourceType', 'task_id'),
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
			'searchEarliest' => 'Earliest',
			'preferred_mon' => 'Mon',
			'preferred_tue' => 'Tue',
			'preferred_wed' => 'Wed',
			'preferred_thu' => 'Thu',
			'preferred_fri' => 'Fri',
			'preferred_sat' => 'Sat',
			'preferred_sun' => 'Sun',
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
			'SELECT DATE_ADD( project.planned, INTERVAL MAX( lead_in_days ) DAY ) AS searchEarliest
				FROM task
				JOIN project ON task.project_id = project.id
				JOIN duty ON task.id = duty.task_id
				JOIN task_type_to_duty_type ON duty.task_type_to_duty_type_id = task_type_to_duty_type_id
				JOIN duty_type ON task_type_to_duty_type.duty_type_id = duty_type.id',
			't.preferred_mon',
			't.preferred_tue',
			't.preferred_wed',
			't.preferred_thu',
			't.preferred_fri',
			't.preferred_sat',
			't.preferred_sun',
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
		$criteria->compare('searchEarliest',Yii::app()->format->toMysqlDate($this->searchEarliest));
		$criteria->compare('t.preferred_mon',Yii::app()->format->toMysqlBool($this->preferred_mon));
		$criteria->compare('t.preferred_tue',Yii::app()->format->toMysqlBool($this->preferred_tue));
		$criteria->compare('t.preferred_wed',Yii::app()->format->toMysqlBool($this->preferred_wed));
		$criteria->compare('t.preferred_thu',Yii::app()->format->toMysqlBool($this->preferred_thu));
		$criteria->compare('t.preferred_fri',Yii::app()->format->toMysqlBool($this->preferred_fri));
		$criteria->compare('t.preferred_sat',Yii::app()->format->toMysqlBool($this->preferred_sat));
		$criteria->compare('t.preferred_sun',Yii::app()->format->toMysqlBool($this->preferred_sun));

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
		$columns[] = 'searchEarliest';
		$columns[] = 'scheduled';
		$columns[] = 'preferred_mon:boolean';
		$columns[] = 'preferred_tue:boolean';
		$columns[] = 'preferred_wed:boolean';
		$columns[] = 'preferred_thu:boolean';
		$columns[] = 'preferred_fri:boolean';
		$columns[] = 'preferred_sat:boolean';
		$columns[] = 'preferred_sun:boolean';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchInCharge', 'searchProject', 'searchTaskType', 'searchEarliest');
	}

	public function beforeSave() {

		// ensure no tampering of scheduling if user doesn't have the rights
		if(!Yii::app()->user->checkAccess('Schedule'))
		{
			// reset
			$this->scheduled = $this->getOldAttributeValue('scheduled');
		}
		if(!empty($this->preferred))
		{
			$this->preferred_mon = in_array('0', $this->preferred);
			$this->preferred_tue = in_array('1', $this->preferred);
			$this->preferred_wed = in_array('2', $this->preferred);
			$this->preferred_thu = in_array('3', $this->preferred);
			$this->preferred_fri = in_array('4', $this->preferred);
			$this->preferred_sat = in_array('5', $this->preferred);
			$this->preferred_sun = in_array('6', $this->preferred);
		}
		
		return parent::beforeSave();
	}

	public function afterFind() {
		// prepare check box row items
		if($this->preferred_mon)
		{
			$this->preferred[] = 0;
		}
		if($this->preferred_tue)
		{
			$this->preferred[] = 1;
		}
		if($this->preferred_wed)
		{
			$this->preferred[] = 2;
		}
		if($this->preferred_thu)
		{
			$this->preferred[] = 3;
		}
		if($this->preferred_fri)
		{
			$this->preferred[] = 4;
		}
		if($this->preferred_sat)
		{
			$this->preferred[] = 5;
		}
		if($this->preferred_sun)
		{
			$this->preferred[] = 6;
		}
	
		parent::afterFind();
	}
}

?>