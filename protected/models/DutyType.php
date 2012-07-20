<?php

/**
 * This is the model class for table "duty_type".
 *
 * The followings are the available columns in table 'duty_type':
 * @property integer $id
 * @property string $description
 * @property integer $lead_in_days
 * @property integer $duty_category_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ClientToTaskTypeToDutyType[] $clientToTaskTypeToDutyTypes
 * @property Dutycategory $dutyCategory
 * @property GenericType $genericType
 * @property Staff $staff
 */
class DutyType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DutyType the static model class
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
		return 'duty_type';
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
			array('lead_in_days, duty_category_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, lead_in_days, duty_category_id, generic_type_id, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'clientToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ClientToTaskTypeToDutyType', 'duty_type_id'),
			'dutyCategory' => array(self::BELONGS_TO, 'Dutycategory', 'duty_category_id'),
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
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
			'description' => 'Description',
			'lead_in_days' => 'Lead In Days',
			'duty_category_id' => 'Duty Category',
			'generic_type_id' => 'Generic Type',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('lead_in_days',$this->lead_in_days);
		$criteria->compare('duty_category_id',$this->duty_category_id);
		$criteria->compare('generic_type_id',$this->generic_type_id);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}