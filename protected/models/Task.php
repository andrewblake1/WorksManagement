<?php

/**
 * This is the model class for table "task".
 *
 * The followings are the available columns in table 'task':
 * @property string $id
 * @property string $description
 * @property string $day
 * @property string $purchase_orders_id
 * @property string $crew_id
 * @property string $project_id
 * @property integer $client_to_task_type_client_id
 * @property integer $client_to_task_type_task_type_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property MaterialToTask[] $materialToTasks
 * @property Reschedule[] $reschedules
 * @property Reschedule[] $reschedules1
 * @property PurchaseOrders $purchaseOrders
 * @property Crew $crew
 * @property Day $day0
 * @property Project $project
 * @property ClientToTaskType $clientToTaskTypeClient
 * @property ClientToTaskType $clientToTaskTypeTaskType
 * @property Staff $staff
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 * @property TaskToResourceType[] $taskToResourceTypes
 * @property TaskType[] $taskTypes
 */
class Task extends ActiveRecord
{
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
			array('description, day, purchase_orders_id, crew_id, project_id, client_to_task_type_client_id, client_to_task_type_task_type_id, staff_id', 'required'),
			array('client_to_task_type_client_id, client_to_task_type_task_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('day, purchase_orders_id, crew_id, project_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, day, purchase_orders_id, crew_id, project_id, client_to_task_type_client_id, client_to_task_type_task_type_id, staff_id', 'safe', 'on'=>'search'),
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
			'purchaseOrders' => array(self::BELONGS_TO, 'PurchaseOrders', 'purchase_orders_id'),
			'crew' => array(self::BELONGS_TO, 'Crew', 'crew_id'),
			'day0' => array(self::BELONGS_TO, 'Day', 'day'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
			'clientToTaskTypeClient' => array(self::BELONGS_TO, 'ClientToTaskType', 'client_to_task_type_client_id'),
			'clientToTaskTypeTaskType' => array(self::BELONGS_TO, 'ClientToTaskType', 'client_to_task_type_task_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
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
		return array(
			'id' => 'ID',
			'description' => 'Description',
			'day' => 'Day',
			'purchase_orders_id' => 'Purchase Orders',
			'crew_id' => 'Crew',
			'project_id' => 'Project',
			'client_to_task_type_client_id' => 'Client To Task Type Client',
			'client_to_task_type_task_type_id' => 'Client To Task Type Task Type',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('day',$this->day,true);
		$criteria->compare('purchase_orders_id',$this->purchase_orders_id,true);
		$criteria->compare('crew_id',$this->crew_id,true);
		$criteria->compare('project_id',$this->project_id,true);
		$criteria->compare('client_to_task_type_client_id',$this->client_to_task_type_client_id);
		$criteria->compare('client_to_task_type_task_type_id',$this->client_to_task_type_task_type_id);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}