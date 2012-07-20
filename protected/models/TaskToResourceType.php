<?php

/**
 * This is the model class for table "task_to_resource_type".
 *
 * The followings are the available columns in table 'task_to_resource_type':
 * @property string $id
 * @property string $task_id
 * @property integer $resource_type_id
 * @property integer $quantity
 * @property integer $hours
 * @property string $start
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property ResourceType $resourceType
 * @property Staff $staff
 */
class TaskToResourceType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TaskToResourceType the static model class
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
		return 'task_to_resource_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, resource_type_id, quantity, hours, staff_id', 'required'),
			array('resource_type_id, quantity, hours, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id', 'length', 'max'=>10),
			array('start', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, resource_type_id, quantity, hours, start, staff_id', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'resourceType' => array(self::BELONGS_TO, 'ResourceType', 'resource_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'task_id' => 'Task',
			'resource_type_id' => 'Resource Type',
			'quantity' => 'Quantity',
			'hours' => 'Hours',
			'start' => 'Start',
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
		$criteria->compare('task_id',$this->task_id,true);
		$criteria->compare('resource_type_id',$this->resource_type_id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('hours',$this->hours);
		$criteria->compare('start',$this->start,true);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}