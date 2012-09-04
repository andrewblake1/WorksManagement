<?php

/**
 * This is the model class for table "staff".
 *
 * The followings are the available columns in table 'staff':
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_mobile
 * @property string $email
 * @property string $password
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AuthAssignment[] $authAssignments
 * @property AuthAssignment[] $authAssignments1
 * @property AuthItem[] $authItems
 * @property AuthItemChild[] $authItemchildren
 * @property Assembly[] $assemblies
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property Client[] $clients
 * @property DefaultValue[] $defaultValues
 * @property Duty[] $duties
 * @property DutyType[] $dutyTypes
 * @property Dutycategory[] $dutycategories
 * @property Generic[] $generics
 * @property GenericProjectType[] $genericProjectTypes
 * @property GenericTaskType[] $genericTaskTypes
 * @property GenericType[] $genericTypes
 * @property Genericprojectcategory[] $genericprojectcategories
 * @property Generictaskcategory[] $generictaskcategories
 * @property Material[] $materials
 * @property MaterialToTask[] $materialToTasks
 * @property Project[] $projects
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property ProjectToProjectTypeToAuthItem[] $projectToProjectTypeToAuthItems
 * @property ProjectType[] $projectTypes
 * @property ProjectTypeToAuthItem[] $projectTypeToAuthItems
 * @property PurchaseOrder[] $purchaseOrders
 * @property Report[] $reports
 * @property ReportToAuthItem[] $reportToAuthItems
 * @property Reschedule[] $reschedules
 * @property ResourceType[] $resourceTypes
 * @property Resourcecategory[] $resourcecategories
 * @property Staff $staff
 * @property Staff[] $staffs
 * @property Supplier[] $suppliers
 * @property Task[] $tasks
 * @property Task[] $tasks1
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 * @property TaskToPurchaseOrder[] $taskToPurchaseOrders
 * @property TaskToResourceType[] $taskToResourceTypes
 * @property TaskType[] $taskTypes
 * @property TaskTypeToAssembly[] $taskTypeToAssemblies
 * @property TaskTypeToDutyType[] $taskTypeToDutyTypes
 * @property TaskTypeToMaterial[] $taskTypeToMaterials
 * @property TaskTypeToResourceType[] $taskTypeToResourceTypes
 */
class Staff extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'staff';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('first_name, last_name, email', 'required'),
			array('deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('first_name, last_name, phone_mobile', 'length', 'max'=>64),
			array('email', 'length', 'max'=>255),
			array('password', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, first_name, last_name, phone_mobile, email, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'authAssignments' => array(self::HAS_MANY, 'AuthAssignment', 'userid'),
			'authAssignments1' => array(self::HAS_MANY, 'AuthAssignment', 'staff_id'),
			'authItems' => array(self::HAS_MANY, 'AuthItem', 'staff_id'),
			'authItemchildren' => array(self::HAS_MANY, 'AuthItemChild', 'staff_id'),
			'assemblies' => array(self::HAS_MANY, 'Assembly', 'staff_id'),
			'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'staff_id'),
			'clients' => array(self::HAS_MANY, 'Client', 'staff_id'),
			'defaultValues' => array(self::HAS_MANY, 'DefaultValue', 'staff_id'),
			'duties' => array(self::HAS_MANY, 'Duty', 'staff_id'),
			'dutyTypes' => array(self::HAS_MANY, 'DutyType', 'staff_id'),
			'dutycategories' => array(self::HAS_MANY, 'Dutycategory', 'staff_id'),
			'generics' => array(self::HAS_MANY, 'Generic', 'staff_id'),
			'genericProjectTypes' => array(self::HAS_MANY, 'GenericProjectType', 'staff_id'),
			'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'staff_id'),
			'genericTypes' => array(self::HAS_MANY, 'GenericType', 'staff_id'),
			'genericprojectcategories' => array(self::HAS_MANY, 'Genericprojectcategory', 'staff_id'),
			'generictaskcategories' => array(self::HAS_MANY, 'Generictaskcategory', 'staff_id'),
			'materials' => array(self::HAS_MANY, 'Material', 'staff_id'),
			'materialToTasks' => array(self::HAS_MANY, 'MaterialToTask', 'staff_id'),
			'projects' => array(self::HAS_MANY, 'Project', 'staff_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'staff_id'),
			'projectToProjectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTypeToAuthItem', 'staff_id'),
			'projectTypes' => array(self::HAS_MANY, 'ProjectType', 'staff_id'),
			'projectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectTypeToAuthItem', 'staff_id'),
			'purchaseOrders' => array(self::HAS_MANY, 'PurchaseOrder', 'staff_id'),
			'reports' => array(self::HAS_MANY, 'Report', 'staff_id'),
			'reportToAuthItems' => array(self::HAS_MANY, 'ReportToAuthItem', 'staff_id'),
			'reschedules' => array(self::HAS_MANY, 'Reschedule', 'staff_id'),
			'resourceTypes' => array(self::HAS_MANY, 'ResourceType', 'staff_id'),
			'resourcecategories' => array(self::HAS_MANY, 'Resourcecategory', 'staff_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'staffs' => array(self::HAS_MANY, 'Staff', 'staff_id'),
			'suppliers' => array(self::HAS_MANY, 'Supplier', 'staff_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'staff_id'),
			'tasks1' => array(self::HAS_MANY, 'Task', 'in_charge_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'staff_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'staff_id'),
			'taskToPurchaseOrders' => array(self::HAS_MANY, 'TaskToPurchaseOrder', 'staff_id'),
			'taskToResourceTypes' => array(self::HAS_MANY, 'TaskToResourceType', 'staff_id'),
			'taskTypes' => array(self::HAS_MANY, 'TaskType', 'staff_id'),
			'taskTypeToAssemblies' => array(self::HAS_MANY, 'TaskTypeToAssembly', 'staff_id'),
			'taskTypeToDutyTypes' => array(self::HAS_MANY, 'TaskTypeToDutyType', 'staff_id'),
			'taskTypeToMaterials' => array(self::HAS_MANY, 'TaskTypeToMaterial', 'staff_id'),
			'taskTypeToResourceTypes' => array(self::HAS_MANY, 'TaskTypeToResourceType', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Staff',
			'first_name' => 'First name',
			'last_name' => 'Last name',
			'phone_mobile' => 'Phone mobile',
			'email' => 'Email',
			'password' => 'Password',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.first_name',$this->first_name,true);
		$criteria->compare('t.last_name',$this->last_name,true);
		$criteria->compare('t.phone_mobile',$this->phone_mobile,true);
		$criteria->compare('t.email',$this->email,true);

		$criteria->select=array(
			't.id',
			't.first_name',
			't.last_name',
			't.phone_mobile',
			't.email',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'first_name';
		$columns[] = 'last_name';
        $columns[] = array(
			'name'=>'phone_mobile',
			'value'=>'CHtml::link($data->phone_mobile, "tel:".$data->phone_mobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'email',
			'value'=>'$data->email',
			'type'=>'email',
		);
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'first_name',
			'last_name',
			'email',
		);
	}

	/**
	 * perform one-way encryption on the password before we store it in the database
	 */
	protected function afterValidate()
	{   
		parent::afterValidate();
		$this->password = $this->encrypt($this->password);                     
	}
	
	public function encrypt($value)
	{
		// if the password value has changed
		return $this->isNewRecord || $this->attributeChanged('password') ? md5($value) : $value;
	}

}

?>