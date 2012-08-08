<?php

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property string $id
 * @property string $description
 * @property string $day
 * @property string $purchase_order_id
 * @property string $crew_id
 * @property string $project_id
 * @property integer $task_type_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property MaterialToTask[] $materialToTasks
 * @property Reschedule[] $reschedules
 * @property Reschedule[] $reschedules1
 * @property PurchaseOrder $purchaseOrder
 * @property Crew $crew
 * @property Day $day0
 * @property Project $project
 * @property Staff $staff
 * @property TaskType $taskType
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 * @property TaskToResourceType[] $taskToResourceTypes
 * @property TaskType[] $taskTypes
 */
class Task extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchPurchaseOrder;
	public $searchCrew;
	public $searchProject;
	public $searchTaskType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Task the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

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
			array('description, day, purchase_order_id, crew_id, project_id, task_type_id, staff_id', 'required'),
			array('task_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('day, purchase_order_id, crew_id, project_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, day, searchPurchaseOrder, searchCrew, searchProject, searchTaskType, searchStaff', 'safe', 'on'=>'search'),
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
			'reschedules' => array(self::HAS_MANY, 'Reschedule', 'task_old'),
			'reschedules1' => array(self::HAS_MANY, 'Reschedule', 'task_new'),
			'purchaseOrder' => array(self::BELONGS_TO, 'PurchaseOrder', 'purchase_order_id'),
			'crew' => array(self::BELONGS_TO, 'Crew', 'crew_id'),
			'day0' => array(self::BELONGS_TO, 'Day', 'day'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'task_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'task_id'),
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
			'day' => 'Day',
			'purchase_order_id' => 'Purchase Order (Supplier/Order number)',
			'searchPurchaseOrder' => 'Purchase Order (Supplier/Order number)',
			'crew_id' => 'Crew (First/Last/Email)',
			'searchCrew' => 'Crew (First/Last/Email)',
			'project_id' => 'Project',
			'searchProject' => 'Project',
			'task_type_id' => 'Task Type (Client/Task type)',
			'searchTaskType' => 'Task Type (Client/Task type)',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('day',$this->day,true);
		$this->compositeCriteria($criteria,
			array(
				'supplier.name',
				'purchaseOrder.number',
			),
			$this->searchPurchaseOrder
		);
		$this->compositeCriteria($criteria,
			array(
				'crew.inCharge.first_name',
				'crew.inCharge.last_name',
				'crew.inCharge.email',
			),
			$this->searchCrew
		);
		$criteria->compare('project.id',$this->searchProject,true);
		$this->compositeCriteria($criteria,
			array(
				'taskType.client.name',
				'taskType.description',
				),
			$this->searchTaskType
		);
		
		$criteria->with = array(
			'purchaseOrder',
			'purchaseOrder.supplier',
			'crew.inCharge',
			'project',
			'taskType.client',
			);

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'description',
			'day',
			"CONCAT_WS('$delimiter',
				supplier.name,
				purchaseOrder.number
				) AS searchPurchaseOrder",
			"CONCAT_WS('$delimiter',
				inCharge.first_name,
				inCharge.last_name,
				inCharge.email
				) AS searchCrew",
			'project.id AS searchProject',
			"CONCAT_WS('$delimiter',
				client.name,
				taskType.description
				) AS searchTaskType",
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchPurchaseOrder', 'searchCrew', 'searchProject', 'searchTaskType');
	}
}