<?php

/**
 * This is the model class for table "client_to_task_type_to_duty_type".
 *
 * The followings are the available columns in table 'client_to_task_type_to_duty_type':
 * @property integer $id
 * @property integer $duty_type_id
 * @property integer $client_to_task_type_id
 * @property string $AuthItem_name
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property DutyType $dutyType
 * @property AuthItem $authItemName
 * @property Staff $staff
 * @property ClientToTaskType $clientToTaskType
 * @property ProjectToAuthAssignmentToClientToTaskTypeToDutyType[] $projectToAuthAssignmentToClientToTaskTypeToDutyTypes
 */
class ClientToTaskTypeToDutyType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ClientToTaskTypeToDutyType the static model class
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
		return 'client_to_task_type_to_duty_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('duty_type_id, client_to_task_type_id, AuthItem_name, staff_id', 'required'),
			array('duty_type_id, client_to_task_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('AuthItem_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, duty_type_id, client_to_task_type_id, AuthItem_name, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'dutyType' => array(self::BELONGS_TO, 'DutyType', 'duty_type_id'),
			'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'AuthItem_name'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'clientToTaskType' => array(self::BELONGS_TO, 'ClientToTaskType', 'client_to_task_type_id'),
			'projectToAuthAssignmentToClientToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ProjectToAuthAssignmentToClientToTaskTypeToDutyType', 'client_to_task_type_to_duty_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'duty_type_id' => 'Duty Type',
			'client_to_task_type_id' => 'Client To Task Type',
			'AuthItem_name' => 'Auth Item Name',
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
		$criteria->compare('duty_type_id',$this->duty_type_id);
		$criteria->compare('client_to_task_type_id',$this->client_to_task_type_id);
		$criteria->compare('AuthItem_name',$this->AuthItem_name,true);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}