<?php

/**
 * This is the model class for table "generic_type".
 *
 * The followings are the available columns in table 'generic_type':
 * @property integer $id
 * @property string $label
 * @property string $mandatory
 * @property integer $allow_new
 * @property string $validation_type_id
 * @property string $data_type
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property DutyType[] $dutyTypes
 * @property GenericProjectType[] $genericProjectTypes
 * @property GenericTaskType[] $genericTaskTypes
 * @property Staff $staff
 */
class GenericType extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GenericType the static model class
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
		return 'generic_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('label, mandatory, validation_type_id, data_type, staff_id', 'required'),
			array('allow_new, staff_id', 'numerical', 'integerOnly'=>true),
			array('label, mandatory', 'length', 'max'=>64),
			array('validation_type_id', 'length', 'max'=>10),
			array('data_type', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, label, mandatory, allow_new, validation_type_id, data_type, staff_id', 'safe', 'on'=>'search'),
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
			'dutyTypes' => array(self::HAS_MANY, 'DutyType', 'generic_type_id'),
			'genericProjectTypes' => array(self::HAS_MANY, 'GenericProjectType', 'generic_type_id'),
			'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'generic_type_id'),
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
			'label' => 'Label',
			'mandatory' => 'Mandatory',
			'allow_new' => 'Allow New',
			'validation_type_id' => 'Validation Type',
			'data_type' => 'Data Type',
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
		$criteria->compare('label',$this->label,true);
		$criteria->compare('mandatory',$this->mandatory,true);
		$criteria->compare('allow_new',$this->allow_new);
		$criteria->compare('validation_type_id',$this->validation_type_id,true);
		$criteria->compare('data_type',$this->data_type,true);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}