<?php

/**
 * This is the model class for table "crew".
 *
 * The followings are the available columns in table 'crew':
 * @property string $id
 * @property string $preferred_date
 * @property string $earliest_date
 * @property string $date_scheduled
 * @property integer $in_charge
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $inCharge
 * @property Staff $staff
 * @property Task[] $tasks
 */
class Crew extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Crew the static model class
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
		return 'crew';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('in_charge, staff_id', 'required'),
			array('in_charge, staff_id', 'numerical', 'integerOnly'=>true),
			array('preferred_date, earliest_date, date_scheduled', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, preferred_date, earliest_date, date_scheduled, in_charge, staff_id', 'safe', 'on'=>'search'),
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
			'inCharge' => array(self::BELONGS_TO, 'Staff', 'in_charge'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'crew_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'preferred_date' => 'Preferred Date',
			'earliest_date' => 'Earliest Date',
			'date_scheduled' => 'Date Scheduled',
			'in_charge' => 'In Charge',
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
		$criteria->compare('preferred_date',$this->preferred_date,true);
		$criteria->compare('earliest_date',$this->earliest_date,true);
		$criteria->compare('date_scheduled',$this->date_scheduled,true);
		$criteria->compare('in_charge',$this->in_charge);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}