<?php

/**
 * This is the model class for table "client_to_task_type".
 *
 * The followings are the available columns in table 'client_to_task_type':
 * @property integer $id
 * @property integer $client_id
 * @property integer $task_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Client $client
 * @property TaskType $taskType
 * @property Staff $staff
 * @property ClientToTaskTypeToDutyType[] $clientToTaskTypeToDutyTypes
 * @property GenericTaskType[] $genericTaskTypes
 * @property GenericTaskType[] $genericTaskTypes1
 * @property Task[] $tasks
 * @property Task[] $tasks1
 */
class ClientToTaskType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ClientToTaskType the static model class
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
		return 'client_to_task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, task_type_id, staff_id', 'required'),
			array('client_id, task_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, task_type_id, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'clientToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ClientToTaskTypeToDutyType', 'client_to_task_type_id'),
			'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'client_to_task_type_client_id'),
			'genericTaskTypes1' => array(self::HAS_MANY, 'GenericTaskType', 'client_to_task_type_task_type_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'client_to_task_type_client_id'),
			'tasks1' => array(self::HAS_MANY, 'Task', 'client_to_task_type_task_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_id' => 'Client',
			'task_type_id' => 'Task Type',
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
		$criteria->compare('client_id',$this->client_id);
		$criteria->compare('task_type_id',$this->task_type_id);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}