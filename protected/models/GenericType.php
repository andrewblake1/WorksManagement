<?php

/**
 * This is the model class for table "generic_type".
 *
 * The followings are the available columns in table 'generic_type':
 * @property integer $id
 * @property string $description
 * @property integer $mandatory
 * @property integer $allow_new
 * @property string $validation_type
 * @property string $data_type
 * @property string $validation_text
 * @property string $validation_error
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
	 * Returns data types.
	 * @return array data storage types - to match enum type in mysql workbench
	 */
	public static function getDataTypes()
	{
		return array(1=>'Date', 2=>'Float' ,3=>'Int', 4=>'Text' , 5=>'Time');
	}

	/**
	 * Returns validation types.
	 * @return array validation types - to match enum type in mysql workbench
	 */
	public static function getValidationTypes()
	{
		return array(1=>'None', 2=>'PCRE', 3=>'SQL select', 4=>'Value list');
	}

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
			array('description, validation_type, data_type, staff_id', 'required'),
			array('mandatory, allow_new, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('validation_type', 'length', 'max'=>10),
			array('data_type', 'length', 'max'=>5),
			array('validation_text, validation_error', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, mandatory, allow_new, validation_type, data_type, validation_text, validation_error, searchStaff', 'safe', 'on'=>'search'),
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
	 * @return array customized attribute descriptions (name=>description)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Generic Type',
			'mandatory' => 'Mandatory',
			'allow_new' => 'Allow New',
			'validation_type' => 'Validation Type',
			'data_type' => 'Data Type',
			'validation_text' => 'Validation Text',
			'validation_error' => 'Validation Error',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('mandatory',$this->mandatory);
		$criteria->compare('allow_new',$this->allow_new);
		$criteria->compare('validation_type',$this->validation_type,true);
		$criteria->compare('data_type',$this->data_type,true);
		$criteria->compare('validation_text',$this->validation_text,true);
		$criteria->compare('validation_error',$this->validation_error,true);

		$criteria->select=array(
			'id',
			'description',
			'mandatory',
			'allow_new',
			'validation_type',
			'data_type',
			'validation_text',
			'validation_error',
		);

		return $criteria;
	}

}