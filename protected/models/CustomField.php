<?php

/**
 * This is the model class for table "tbl_custom_field".
 *
 * The followings are the available columns in table 'tbl_custom_field':
 * @property integer $id
 * @property string $label
 * @property integer $mandatory
 * @property integer $allow_new
 * @property string $validation_type
 * @property string $data_type
 * @property string $validation_text
 * @property string $validation_error
 * @property string $default_select
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property CustomFieldToProjectTemplate[] $customFieldToProjectTemplates
 * @property CustomFieldToTaskTemplate[] $customFieldToTaskTemplates
 * @property DutyStep[] $dutySteps
 */
class CustomField extends ActiveRecord
{
	/**
	 * Data types. These are the emum values set by the data_type Custom field within 
	 * the database
	 */
	const data_typeDate = 'Date';
	const data_typeFloat = 'Float';
	const data_typeInt = 'Int';
	const data_typeText = 'Text';
	const data_typeTime  = 'Time';

	/**
	 * Validation types. These are the emum values set by the ValidationType Custom field within
	 * the database
	 */
	const validation_typeNone = 'None';
	const validation_typePCRE = 'PCRE';
	const validation_typeRange = 'Range';
	const validation_typeSQLSelect = 'SQL select';
	const validation_typeValueList = 'Value list';

	/**
	 * Returns data type labels.
	 * @return array data storage types - to match enum type in mysql workbench
	 */
	public static function getDataTypeLabels()
	{
		return array(
			self::data_typeDate=>self::data_typeDate,
			self::data_typeFloat=>self::data_typeFloat,
			self::data_typeInt=>self::data_typeInt,
			self::data_typeText=>self::data_typeText,
			self::data_typeTime=>self::data_typeTime,
		);
	}

	/**
	 * Returns validation types.
	 * @return array validation types - to match enum type in mysql workbench
	 */
	public static function getValidationTypeLabels()
	{
		return array(
			self::validation_typeNone=>self::validation_typeNone,
			self::validation_typePCRE=>self::validation_typePCRE,
			self::validation_typeRange=>self::validation_typeRange,
			self::validation_typeSQLSelect=>self::validation_typeSQLSelect,
			self::validation_typeValueList=>self::validation_typeValueList,
		);
	}

	/**
	* @param string $attribute the name of the attribute to be validated
	* @param array $params options specified in the validation rule
	*/
	public function validation_text($attribute, $params)
	{
		$errorMessage = NULL;
		
		switch($this->validation_type)
		{
			// Value list
			case self::validation_typeValueList :
				if(!$this->$attribute)
					$errorMessage = 'You must provide at least one value or several values seperated by a comma.';
				break;
				
			// Perl compatible regular expression
			case self::validation_typePCRE :
				// Turn off error reporting
				$oldError = error_reporting(0);
				if(preg_match($this->$attribute, '') === false) 
				{
					$errorMessage = 'There is an error in your expression. Remember this
						is perl compatible flavor which means the first and last characters must be the same ( / or
						# are commonly used e.g. #\d+# or /^[0-9]*$/ ). The error was:<br>'.preg_last_error();
				}
				// Set error reporting to old level
				error_reporting($oldEerror);
				break;
				
			// Numeric range
			case self::validation_typeRange :
				if(!preg_match('/^\s*\d+\s*[\-]\s*\d+\s*$/', $this->$attribute))
					$errorMessage = 'Invalid range given - must be positive integers
						seperated by a hyhpen(-) e.g. 5-10.';
				break;
				
  			// SQL select
			case self::validation_typeSQLSelect:
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'customFieldToProjectTemplates' => array(self::HAS_MANY, 'CustomFieldToProjectTemplate', 'custom_field_id'),
            'customFieldToTaskTemplates' => array(self::HAS_MANY, 'CustomFieldToTaskTemplate', 'custom_field_id'),
            'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'custom_field_id'),
        );
    }

	/**
	 * @return array customized attribute descriptions (name=>description)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'mandatory' => 'Mandatory',
			'allow_new' => 'Allow new',
			'validation_type' => 'Validation type',
			'data_type' => 'Data type',
			'validation_text' => 'Validation text',
			'validation_error' => 'Validation error',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.label',$this->label,true);
		$criteria->compare('t.mandatory',Yii::app()->format->toMysqlBool($this->mandatory));
		$criteria->compare('t.allow_new',Yii::app()->format->toMysqlBool($this->allow_new));
		$criteria->compare('t.validation_type',$this->validation_type,true);
		$criteria->compare('t.data_type',$this->data_type,true);
		$criteria->compare('t.validation_text',$this->validation_text,true);
		$criteria->compare('t.validation_error',$this->validation_error,true);

		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.label',
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
		$columns[] = $this->linkThisColumn('label');
		$columns[] = 'mandatory:boolean';
		$columns[] = 'allow_new:boolean';
		$columns[] = 'validation_type';
		$columns[] = 'data_type';
		$columns[] = 'validation_text';
		$columns[] = 'validation_error';
		
		return $columns;
	}
	
	public static function getDisplayAttr()
	{
		return array('label');
	}

}

?>