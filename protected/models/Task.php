<?php

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property string $id
 * @property string $level
 * @property string $project_id
 * @property integer $task_type_id
 * @property integer $client_id
 * @property string $planned
 * @property string $location
 * @property integer $preferred_mon
 * @property integer $preferred_tue
 * @property integer $preferred_wed
 * @property integer $preferred_thu
 * @property integer $preferred_fri
 * @property integer $preferred_sat
 * @property integer $preferred_sun
 * @property string $crew_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property MaterialToTask[] $materialToTasks
 * @property MaterialToTask[] $materialToTasks1
 * @property Project $project
 * @property Project $client
 * @property Staff $staff
 * @property TaskType $taskType
 * @property Planning $id0
 * @property TaskLevel $level0
 * @property Crew $crew
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
	public $name;
	public $in_charge_id;
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
			array('project_id, task_type_id, client_id, crew_id, staff_id', 'required'),
			array('task_type_id, client_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('id, level, in_charge_id, project_id, crew_id', 'length', 'max'=>10),
			array('planned, preferred, name, location', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, level, searchInCharge, searchEarliest, searchProject, searchTaskType, searchStaff, name, crew_id, planned, location, preferred_mon, preferred_tue, preferred_wed, preferred_thu, preferred_fri, preferred_sat, preferred_sun', 'safe', 'on'=>'search'),
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
			'materialToTasks' => array(self::HAS_MANY, 'MaterialToTask', 'client_id'),
			'materialToTasks1' => array(self::HAS_MANY, 'MaterialToTask', 'task_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'client' => array(self::BELONGS_TO, 'Project', 'client_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
			'level0' => array(self::BELONGS_TO, 'TaskLevel', 'level'),
			'crew' => array(self::BELONGS_TO, 'Crew', 'crew_id'),
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
			'name' => 'Task',
			'location' => 'Location',
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
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			't.task_type_id',
			'id0.name AS name',
			't.location',
			't.planned',
//			'DATE_ADD( project.planned, INTERVAL MAX( lead_in_days ) DAY ) AS searchEarliest',
			'(SELECT `date` FROM working_days WHERE id = (SELECT id + MAX( lead_in_days ) FROM working_days WHERE `date` >= t.planned LIMIT 1)) as searchEarliest',
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

		// group
		$criteria->group = 't.id';
		// join 
		$criteria->join='
			LEFT JOIN duty ON t.id = duty.task_id
			LEFT JOIN task_type_to_duty_type ON duty.task_type_to_duty_type_id = task_type_to_duty_type_id
			LEFT JOIN duty_type ON task_type_to_duty_type.duty_type_id = duty_type.id';

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('t.location',$this->location,true);
		$criteria->compare('t.planned',Yii::app()->format->toMysqlDate($this->planned));
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
		$criteria->compare('t.crew_id',$this->crew_id);
		
		// join
		$criteria->with = array(
			'id0',
			'id0.inCharge',
			'project',
			'taskType',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'name';
		$columns[] = 'location';
        $columns[] = static::linkColumn('searchInCharge', 'Staff', 'in_charge_id');
        $columns[] = static::linkColumn('searchTaskType', 'TaskType', 'task_type_id');
		$columns[] = 'planned';
		$columns[] = 'searchEarliest:date';
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
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='id0->name';

		return $displaAttr;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchInCharge', 'searchProject', 'searchTaskType', 'searchEarliest', 'name');
	}

	public function beforeSave() {

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

		$this->id0->name = $this->name;
		$this->id0->in_charge_id = $this->in_charge_id;
			
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

		$this->name = $this->id0->name;
		$this->in_charge_id = $this->id0->in_charge_id;
	
		parent::afterFind();
	}
	
	/*
	 * can't set default value in database as TEXT data type but is required
	 */
	public function init()
	{
		// can't set default value in database as TEXT data type for AuthItem
		$this->planned = date('d M, Y');
		
		parent::init();
	}

	/*
	 * can't set default value in database as TEXT data type but is required
	 */
	public function beforeValidate()
	{
// TODO: possibly may not need project_id in task unless circular constraint
		$crew = Crew::model()->findByPk($this->crew_id);
//		$this->project_id = $_SESSION['actionAdminGet']['Day']['project_id'];
		$this->project_id = $crew->day->project_id;

		$project = Project::model()->findByPk($this->project_id);
		$this->client_id = $project->client_id;
		
		return parent::beforeValidate();
	}


	// ensure that where possible a pk has been passed from parent
	// needed to overwrite this here because project has to look thru project type to get to client when doing update but gets client for admin
	public function assertFromParent()
	{
		// if we are in the schdule screen then they may not be a parent foreign key as will be derived when user identifies a node
		if(!(Yii::app()->controller->id == 'planning'))
		{
			return parent::assertFromParent();
		}
	}

}

?>