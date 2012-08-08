<?php

/**
 * This is the model class for table "generic".
 *
 * The followings are the available columns in table 'generic':
 * @property string $id
 * @property integer $type_int
 * @property double $type_float
 * @property string $type_time
 * @property string $type_date
 * @property string $type_text
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property Staff $staff
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 */
class Generic extends ActiveRecord
{
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Generic the static model class
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
		return 'generic';
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
			array('type_int, staff_id', 'numerical', 'integerOnly'=>true),
			array('type_float', 'numerical'),
			array('type_text', 'length', 'max'=>255),
			array('type_time, type_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type_int, type_float, type_time, type_date, type_text, searchStaff', 'safe', 'on'=>'search'),
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
			'duties' => array(self::HAS_MANY, 'Duty', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'generic_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'generic_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Generic',
			'type_int' => 'Type Int',
			'type_float' => 'Type Float',
			'type_time' => 'Type Time',
			'type_date' => 'Type Date',
			'type_text' => 'Type Text',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('type_int',$this->type_int);
		$criteria->compare('type_float',$this->type_float);
		$criteria->compare('type_time',$this->type_time,true);
		$criteria->compare('type_date',$this->type_date,true);
		$criteria->compare('type_text',$this->type_text,true);

		$criteria->select=array(
			'id',
			'type_int',
			'type_float',
			'type_time',
			'type_date',
			'type_text',
		);

		return $criteria;
	}

}