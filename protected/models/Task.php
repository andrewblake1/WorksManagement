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
 * @property string $purchase_order_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property MaterialToTask[] $materialToTasks
 * @property Reschedule[] $reschedules
 * @property Reschedule[] $reschedules1
 * @property PurchaseOrder $purchaseOrder
 * @property Project $project
 * @property Staff $staff
 * @property TaskType $taskType
 * @property Staff $inCharge
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
	public $searchInCharge;
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
			array('description, project_id, task_type_id, in_charge_id, staff_id', 'required'),
			array('task_type_id, in_charge_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_id, purchase_order_id', 'length', 'max'=>10),
			array('planned, scheduled, earliest, preferred', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('searchPurchaseOrder, searchInCharge, searchProject, searchTaskType, searchStaff, id, description, project_id, planned, scheduled, earliest, preferred', 'safe', 'on'=>'search'),
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
			'purchaseOrder' => array(self::BELONGS_TO, 'PurchaseOrder', 'purchase_order_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'inCharge' => array(self::BELONGS_TO, 'Staff', 'in_charge_id'),
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
			'purchase_order_id' => 'Supplier/Purchase order number',
			'searchPurchaseOrder' => 'Supplier/Purchase order number',
			'in_charge_id' => 'In charge, First/Last/Email',
			'searchInCharge' => 'In charge, First/Last/Email',
			'project_id' => 'Client/Project',
			'searchProject' => 'Client/Project',
			'task_type_id' => 'Project type/Task type',
			'searchTaskType' => 'Project type/Task type',
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
			't.description',
			't.planned',
			't.scheduled',
			't.earliest',
			't.preferred',
			"CONCAT_WS('$delimiter',
				supplier.name,
				purchaseOrder.number
				) AS searchPurchaseOrder",
			"CONCAT_WS('$delimiter',
				inCharge.first_name,
				inCharge.last_name,
				inCharge.email
				) AS searchInCharge",
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.planned',$this->planned);
		$criteria->compare('t.scheduled',$this->scheduled);
		$criteria->compare('t.earliest',$this->earliest);
		$criteria->compare('t.preferred',$this->preferred);
		$this->compositeCriteria($criteria,
			array(
				'supplier.name',
				'purchaseOrder.number',
			),
			$this->searchPurchaseOrder
		);
		$this->compositeCriteria($criteria,
			array(
				'inCharge.first_name',
				'inCharge.last_name',
				'inCharge.email',
			),
			$this->searchInCharge
		);
		if(isset($this->project_id))
		{
			$criteria->compare('t.project_id',$this->project_id);

			ActiveRecord::$labelOverrides['searchTaskType'] = 'Task type';
			$criteria->select[]="CONCAT_WS('$delimiter',
				taskType.description
				) AS searchTaskType";
			$this->compositeCriteria($criteria,
				array(
					'taskType.description',
				),
				$this->searchTaskType
			);
		}
		else
		{
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
				project.description
				) AS searchProject";
			$this->compositeCriteria($criteria,
				array(
					'client.name',
					'project.description',
				),
				$this->searchProject
			);
			$criteria->select[]="CONCAT_WS('$delimiter',
				projectType.description,
				taskType.description
				) AS searchTaskType";
			$this->compositeCriteria($criteria,
				array(
					'projectType.description',
					'taskType.description',
				),
				$this->searchTaskType
			);
		}
		
		// join
		$criteria->with = array(
			'purchaseOrder',
			'purchaseOrder.supplier',
			'inCharge',
			'project',
			'project.projectType',
			'taskType',
			'project.projectType.client',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
        $columns[] = array(
			'name'=>'searchPurchaseOrder',
			'value'=>'CHtml::link($data->searchPurchaseOrder,
				Yii::app()->createUrl("PurchaseOrder/update", array("id"=>$data->purchase_order_id))
			)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchInCharge',
			'value'=>'CHtml::link($data->searchInCharge,
				Yii::app()->createUrl("Staff/update", array("id"=>$data->in_charge_id))
			)',
			'type'=>'raw',
		);
		if(!isset($this->project_id))
		{
			$columns[] = array(
				'name'=>'searchProject',
				'value'=>'CHtml::link($data->searchProject,
					Yii::app()->createUrl("Project/update", array("id"=>$data->project_id))
				)',
				'type'=>'raw',
			);
		}
        $columns[] = array(
			'name'=>'searchTaskType',
			'value'=>'CHtml::link($data->searchTaskType,
				Yii::app()->createUrl("TaskTyp/update", array("id"=>$data->task_type_id))
			)',
			'type'=>'raw',
		);
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
		return array('searchPurchaseOrder', 'searchInCharge', 'searchProject', 'searchTaskType');
	}
	
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		// if this pk attribute has been passed in a higher crumb in the breadcrumb trail
		if(Yii::app()->getController()->primaryKeyInBreadCrumbTrail('project_id'))
		{
			ActiveRecord::$labelOverrides['task_id'] = 'Task';
		}
		else
		{
			ActiveRecord::$labelOverrides['task_id'] = 'Client/Project/Task';
			$displaAttr['project->projectType->client']='name';
			$displaAttr['project']='description';
		}

		$displaAttr[]='description';

		return $displaAttr;
	}

}

?>