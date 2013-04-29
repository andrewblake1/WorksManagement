<?php

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property string $id
 * @property string $level
 * @property string $project_id
 * @property integer $task_type_id
 * @property integer $quantity
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
 * @property Crew $crew
 * @property Planning $id0
 * @property TaskLevel $level0
 * @property Project $project
 * @property Staff $staff
 * @property TaskType $taskType
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 * @property TaskToPurchaseOrder[] $taskToPurchaseOrders
 * @property TaskToResourceType[] $taskToResourceTypes
 */
class Task extends GenericExtensionActiveRecord
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

	protected $class_ModelToGenericModelType = 'TaskToGenericTaskType';
	protected $attribute_generic_model_type_id = 'generic_task_type_id';
	protected $attribute_model_id = 'task_id';
	protected $relation_genericModelType = 'genericTaskType';
	protected $relation_genericModelTypes = 'genericTaskTypes';
	protected $relation_modelType = 'taskType';
	protected $relation_modelToGenericModelTypes = 'taskToGenericTaskTypes';
	protected $relation_modelToGenericModelType = 'taskToGenericTaskType';

	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id, task_type_id, crew_id', 'required'),
			array('task_type_id, quantity', 'numerical', 'integerOnly'=>true),
			array('id, level, in_charge_id, project_id, crew_id', 'length', 'max'=>10),
			array('planned, preferred, name, location', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, level, quantity, searchInCharge, searchEarliest, searchProject, searchTaskType, name, crew_id, planned, location, preferred_mon, preferred_tue, preferred_wed, preferred_thu, preferred_fri, preferred_sat, preferred_sun', 'safe', 'on'=>'search'),
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
			'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'task_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
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
			't.task_type_id',
			't.quantity',
			't.location',
			'COALESCE(t.planned, project.planned) AS planned',
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
			LEFT JOIN duty_type dutyType ON duty.duty_type_id = dutyType.id';

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('t.location',$this->location,true);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('planned',Yii::app()->format->toMysqlDate($this->planned));
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
//$t = $this->crew_id;
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
		$columns[] = $this->linkThisColumn('id');
		$columns[] = $this->linkThisColumn('name');
		$columns[] = 'quantity';
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
		$displaAttr[]='id';
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
		$this->project_id = $crew->day->project_id;
		
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

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		// get the planning model
		$planning = Planning::model()->findByPk($this->id);
		$planning->name = $this->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		// atempt save
		$saved = $planning->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved & parent::updateSave($models);
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		// need to insert a row into the planning nested set model so that the id can be used here
		
		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$planning = new Planning;
		$planning->name = $this->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];

		if($saved = $planning->appendTo(Planning::model()->findByPk($this->crew_id)))
		{
			$this->id = $planning->id;
			$this->quantity = $this->taskType->quantity;
			// parent create save will add generics -- all we need to do is take care care of adding the other things if no errors
			// NB: by calling the parent this is added into $models
			if($saved = parent::createSave($models))
			{
				// attempt creation of resources
				$saved &= $this->createResources($models);
				// attempt creation of assemblies
				$saved &= $this->createAssemblies($models);
				// attempt creation of materials
				$saved &= $this->createMaterials($models);
				// attempt creation of duties
				$saved &= $this->createDutys($models);
			}
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved;
	}

// TODO: replace these with trigger after insert on model. Also cascade delete on these 3 tables
// Also update triggers possibly to maintain ref integ. easiest for now in application code but not great for integrity.
	
	/**
	 * Creates the intial resource rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createResources(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($this->taskType->taskTypeToResourceTypes as $taskTypeToResourceType)
		{
			// create a new resource
			$taskToResourceType = new TaskToResourceType();
			// copy any useful attributes from
			$taskToResourceType->attributes = $taskTypeToResourceType->attributes;
			$taskToResourceType->staff_id = null;
			$taskToResourceType->task_id = $this->id;
			$saved &= $taskToResourceType->createSave($models, $taskTypeToResourceType);
		}
		
		return $saved;
	}

	/**
	 * Append assemblies to task.
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createAssemblies(&$models=array())
	{
		// initialise the saved variable to show no errors
		$saved = true;
		
		// loop thru all all assemblies related to the tasks type
		foreach($this->taskType->taskTypeToAssemblies as $taskTypeToAssembly)
		{
			$saved = TaskToAssemblyController::addAssembly($this->id, $taskTypeToAssembly->assembly_id, $taskTypeToAssembly->quantity, null, null, $models);
		}
		
		return $saved;
	} 
	
	/**
	 * Creates the intial material rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createMaterials(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($this->taskType->taskTypeToMaterials as $taskTypeToMaterial)
		{
			// create a new materials
			$taskToMaterial = new TaskToMaterial();
			// copy any useful attributes from
			$taskToMaterial->attributes = $taskTypeToMaterial->attributes;
			$taskToMaterial->staff_id = null;
			$taskToMaterial->task_id = $this->id;
			// need dummy store id to get around rules
			$taskToMaterial->store_id = 0;
			$saved &= $taskToMaterial->createSave($models);
		}
		
		return $saved;
	}

	/**
	 * Creates the intial duty rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createDutys(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($this->taskType->taskTypeToDutyTypes as $taskTypeToDutyType)
		{
			// create a new duty
			$duty = new Duty();
			// copy any useful attributes from
			$duty->attributes = $taskTypeToDutyType->attributes;
			$duty->staff_id = null;
			$duty->task_id = $this->id;
			$saved &= $duty->createSave($models);
		}
		
		return $saved;
	}
	


}

?>