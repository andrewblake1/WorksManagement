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
 * @property Assembly[] $assemblies
 * @property Client[] $clients
 * @property ClientToTaskType[] $clientToTaskTypes
 * @property ClientToTaskTypeToDutyType[] $clientToTaskTypeToDutyTypes
 * @property Crew[] $crews
 * @property Crew[] $crews1
 * @property Day[] $days
 * @property Duty[] $duties
 * @property DutyType[] $dutyTypes
 * @property Generic[] $generics
 * @property GenericProjectType[] $genericProjectTypes
 * @property GenericTaskType[] $genericTaskTypes
 * @property GenericType[] $genericTypes
 * @property Genericprojectcategory[] $genericprojectcategories
 * @property Generictaskcategory[] $generictaskcategories
 * @property Material[] $materials
 * @property MaterialToTask[] $materialToTasks
 * @property Plan[] $plans
 * @property Project[] $projects
 * @property ProjectToAuthAssignment[] $projectToAuthAssignments
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property PurchaseOrders[] $purchaseOrders
 * @property Reschedule[] $reschedules
 * @property Staff $staff
 * @property Staff[] $staffs
 * @property Supplier[] $suppliers
 * @property Task[] $tasks
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 * @property TaskToResourceType[] $taskToResourceTypes
 * @property TaskType[] $taskTypes
 */
class Staff extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Staff the static model class
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
			array('id, first_name, last_name, phone_mobile, email, password, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'assemblies' => array(self::HAS_MANY, 'Assembly', 'staff_id'),
			'clients' => array(self::HAS_MANY, 'Client', 'staff_id'),
			'clientToTaskTypes' => array(self::HAS_MANY, 'ClientToTaskType', 'staff_id'),
			'clientToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ClientToTaskTypeToDutyType', 'staff_id'),
			'crews' => array(self::HAS_MANY, 'Crew', 'in_charge'),
			'crews1' => array(self::HAS_MANY, 'Crew', 'staff_id'),
			'days' => array(self::HAS_MANY, 'Day', 'staff_id'),
			'duties' => array(self::HAS_MANY, 'Duty', 'staff_id'),
			'dutyTypes' => array(self::HAS_MANY, 'DutyType', 'staff_id'),
			'generics' => array(self::HAS_MANY, 'Generic', 'staff_id'),
			'genericProjectTypes' => array(self::HAS_MANY, 'GenericProjectType', 'staff_id'),
			'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'staff_id'),
			'genericTypes' => array(self::HAS_MANY, 'GenericType', 'staff_id'),
			'genericprojectcategories' => array(self::HAS_MANY, 'Genericprojectcategory', 'staff_id'),
			'generictaskcategories' => array(self::HAS_MANY, 'Generictaskcategory', 'staff_id'),
			'materials' => array(self::HAS_MANY, 'Material', 'staff_id'),
			'materialToTasks' => array(self::HAS_MANY, 'MaterialToTask', 'staff_id'),
			'plans' => array(self::HAS_MANY, 'Plan', 'staff_id'),
			'projects' => array(self::HAS_MANY, 'Project', 'staff_id'),
			'projectToAuthAssignments' => array(self::HAS_MANY, 'ProjectToAuthAssignment', 'staff_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'staff_id'),
			'purchaseOrders' => array(self::HAS_MANY, 'PurchaseOrders', 'staff_id'),
			'reschedules' => array(self::HAS_MANY, 'Reschedule', 'staff_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'staffs' => array(self::HAS_MANY, 'Staff', 'staff_id'),
			'suppliers' => array(self::HAS_MANY, 'Supplier', 'staff_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'staff_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'staff_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'staff_id'),
			'taskToResourceTypes' => array(self::HAS_MANY, 'TaskToResourceType', 'staff_id'),
			'taskTypes' => array(self::HAS_MANY, 'TaskType', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'phone_mobile' => 'Phone Mobile',
			'email' => 'Email',
			'password' => 'Password',
			'deleted' => 'Deleted',
			'staff_id' => 'Staff',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('phone_mobile',$this->phone_mobile,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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