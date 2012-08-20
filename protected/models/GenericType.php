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
	 * Data types. These are the emum values set by the DataType custom type within 
	 * the database
	 */
	const dataTypeDate = 'Date';
	const dataTypeFloat = 'Float';
	const dataTypeInt = 'Int';
	const dataTypeText = 'Text';
	const dataTypeTime  = 'Time';

	/**
	 * Validation types. These are the emum values set by the ValidationType custom type within
	 * the database
	 */
	const validationTypeNone = 'None';
	const validationTypePCRE = 'PCRE';
	const validationTypeRange = 'Range';
	const validationTypeSQLSelect = 'SQL select';
	const validationTypeValueList = 'Value list';

	/**
	 * Returns data type labels.
	 * @return array data storage types - to match enum type in mysql workbench
	 */
	public static function getDataTypeLabels()
	{
		return array(
			self::dataTypeDate=>'Date',
			self::dataTypeFloat=>'Float',
			self::dataTypeInt=>'Int',
			self::dataTypeText=>'Text' ,
			self::dataTypeTime=>'Time',
		);
	}

	/**
	 * Returns data type column names.
	 * @return array data storage types - to match enum type in mysql workbench
	 */
	public static function getDataTypeColumnNames()
	{
		return array(
			self::dataTypeDate=>'type_date',
			self::dataTypeFloat=>'type_float',
			self::dataTypeInt=>'type_int',
			self::dataTypeText=>'type_text' ,
			self::dataTypeTime=>'type_time',
		);
	}

	/**
	 * Returns validation types.
	 * @return array validation types - to match enum type in mysql workbench
	 */
	public static function getValidationTypeLabels()
	{
		return array(
			self::validationTypeNone=>'None',
			self::validationTypePCRE=>'PCRE',
			self::validationTypeRange=>'Range',
			self::validationTypeSQLSelect=>'SQL select',
			self::validationTypeValueList=>'Value list'
		);
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
			array('validation_text', 'validationText'),
		);
	}

	/**
	* @param string $attribute the name of the attribute to be validated
	* @param array $params options specified in the validation rule
	*/
	public function validationText($attribute, $params)
	{
		$errorMessage = NULL;
		
		switch($this->validation_type)
		{
			// Value list
			case self::validationTypeValueList :
				if(!$this->$attribute)
					$errorMessage = 'You must provide at least one value or several values seperated by a comma.';
				break;
				
			// Perl compatible regular expression
			case self::validationTypePCRE :
				// Turn off error reporting
				$old_error = error_reporting(0);
				if(preg_match($this->$attribute, '') === false) 
				{
					$errorMessage = 'There is an error in your expression. Remember this
						is perl compatible flavor which means the first and last characters must be the same ( / or
						# are commonly used e.g. #\d+# or /^[0-9]*$/ ). The error was:<br>'.preg_last_error();
				}
				// Set error reporting to old level
				error_reporting($old_error);
				break;
				
			// Numeric range
			case self::validationTypeRange :
				if(!preg_match('/^\s*\d+\s*[\-]\s*\d+\s*$/', $this->$attribute))
					$errorMessage = 'Invalid range given - must be positive integers
						seperated by a hyhpen(-) e.g. 5-10.';
				break;
				
  			// SQL select
			case self::validationTypeSQLSelect:
//TODO: open another database connection as this user whenever entering user entered sql.
//otherwise they can run their sql with full application access rights
				// test if sql is valid
				try
				{
					// if no rows returned
					if(!sizeof(Yii::app()->db->createCommand($this->$attribute)->queryAll()))
						$errorMessage = 'No rows are being returned by your query which means there is nothing to select.';
				}
				catch(Exception $e)
				{
					$errorMessage = 'You have an error in your expression, the database says:<br> '.$e->getMessage();
				}
				break;
		}
		
		// if validation failed
		if($errorMessage)
			$this->addError($attribute, $errorMessage);
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
			'id' => 'Generic type',
			'mandatory' => 'Mandatory',
			'allow_new' => 'Allow new',
			'validation_type' => 'Validation type',
			'data_type' => 'Data type',
			'validation_text' => 'Validation text',
			'validation_error' => 'Validation error',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

//		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.mandatory',$this->mandatory);
		$criteria->compare('t.allow_new',$this->allow_new);
		$criteria->compare('t.validation_type',$this->validation_type,true);
		$criteria->compare('t.data_type',$this->data_type,true);
		$criteria->compare('t.validation_text',$this->validation_text,true);
		$criteria->compare('t.validation_error',$this->validation_error,true);

		$criteria->select=array(
//			't.id',
			't.description',
			't.mandatory',
			't.allow_new',
			't.validation_type',
			't.data_type',
			't.validation_text',
			't.validation_error',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = 'description';
		$columns[] = 'mandatory';
		$columns[] = 'allow_new';
		$columns[] = 'validation_type';
		$columns[] = 'data_type';
		$columns[] = 'validation_text';
		$columns[] = 'validation_error';
		
		return $columns;
	}

}

?>