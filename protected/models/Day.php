<?php

/**
 * This is the model class for table "day".
 *
 * The followings are the available columns in table 'day':
 * @property string $id
 * @property string $scheduled
 * @property string $preferred
 * @property string $earliest
 * @property string $planned
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property Task[] $tasks
 */
class Day extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Day the static model class
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
		return 'day';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('scheduled, preferred, earliest, planned', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, scheduled, preferred, earliest, planned, searchStaff', 'safe', 'on'=>'search'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'day'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Day',
			'scheduled' => 'Scheduled',
			'preferred' => 'Preferred',
			'earliest' => 'Earliest',
			'planned' => 'Planned',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('scheduled',$this->scheduled,true);
		$criteria->compare('preferred',$this->preferred,true);
		$criteria->compare('earliest',$this->earliest,true);
		$criteria->compare('planned',$this->planned,true);

		$criteria->select=array(
			'id',
			'scheduled',
			'preferred',
			'earliest',
			'planned',
		);

		return $criteria;
	}

}