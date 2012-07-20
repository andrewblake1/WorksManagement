<?php

/**
 * This is the model class for table "task_to_generic_task_type".
 *
 * The followings are the available columns in table 'task_to_generic_task_type':
 * @property string $id
 * @property string $task_id
 * @property integer $generic_task_type_id
 * @property string $generic_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property Generic $generic
 * @property Staff $staff
 * @property GenericTaskType $genericTaskType
 */
class TaskToGenericTaskType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TaskToGenericTaskType the static model class
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
		return 'task_to_generic_task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, generic_task_type_id, generic_id, staff_id', 'required'),
			array('generic_task_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id, generic_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, generic_task_type_id, generic_id, staff_id', 'safe', 'on'=>'search'),
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
			'generic' => array(self::BELONGS_TO, 'Generic', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'genericTaskType' => array(self::BELONGS_TO, 'GenericTaskType', 'generic_task_type_id'),
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
			'generic_task_type_id' => 'Generic Task Type',
			'generic_id' => 'Generic',
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
		$criteria->compare('generic_task_type_id',$this->generic_task_type_id);
		$criteria->compare('generic_id',$this->generic_id,true);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}