<?php

/**
 * This is the model class for table "reschedule".
 *
 * The followings are the available columns in table 'reschedule':
 * @property string $id
 * @property string $task_old
 * @property string $task_new
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $taskOld
 * @property Task $taskNew
 * @property Staff $staff
 */
class Reschedule extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Reschedule the static model class
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
		return 'reschedule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_old, task_new, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('task_old, task_new', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_old, task_new, staff_id', 'safe', 'on'=>'search'),
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
			'taskOld' => array(self::BELONGS_TO, 'Task', 'task_old'),
			'taskNew' => array(self::BELONGS_TO, 'Task', 'task_new'),
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
			'task_old' => 'Task Old',
			'task_new' => 'Task New',
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
		$criteria->compare('task_old',$this->task_old,true);
		$criteria->compare('task_new',$this->task_new,true);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}