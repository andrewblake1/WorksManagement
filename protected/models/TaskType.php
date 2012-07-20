<?php

/**
 * This is the model class for table "task_type".
 *
 * The followings are the available columns in table 'task_type':
 * @property integer $id
 * @property string $description
 * @property integer $deleted
 * @property integer $staff_id
 * @property string $template_task_id
 *
 * The followings are the available model relations:
 * @property ClientToTaskType[] $clientToTaskTypes
 * @property Staff $staff
 * @property Task $templateTask
 */
class TaskType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TaskType the static model class
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
		return 'task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, staff_id', 'required'),
			array('deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('template_task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, deleted, staff_id, template_task_id', 'safe', 'on'=>'search'),
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
			'clientToTaskTypes' => array(self::HAS_MANY, 'ClientToTaskType', 'task_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'templateTask' => array(self::BELONGS_TO, 'Task', 'template_task_id'),
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
			'deleted' => 'Deleted',
			'staff_id' => 'Staff',
			'template_task_id' => 'Template Task',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);
		$criteria->compare('template_task_id',$this->template_task_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}