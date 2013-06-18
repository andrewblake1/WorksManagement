<?php

/**
 * This is the model class for table "tbl_custom_value".
 *
 * The followings are the available columns in table 'tbl_custom_value':
 * @property string $id
 * @property integer $type_int
 * @property double $type_float
 * @property string $type_time
 * @property string $type_date
 * @property string $type_text
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property DutyData[] $dutyDatas
 * @property ProjectToCustomFieldToProjectTemplate[] $projectToCustomFieldToProjectTemplates
 * @property TaskToCustomFieldToTaskTemplate[] $taskToCustomFieldToTaskTemplates
 */
class CustomValue extends ActiveRecord
{
	public $label = '';
	public $html_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('type_int', 'numerical', 'integerOnly'=>true),
			array('type_float', 'numerical'),
			array('type_text', 'length', 'max'=>255),
			array('type_time, type_date', 'safe'),
		));
	}

	public function setCustomValidators($customValidatorParams = array())
	{
		// avoid the call to this function from ActiveRecord::beforeValidate
		// Todo: maybe should override beforevalidate?
		if(empty($customValidatorParams))
		{
			return;
		}

		$customField = $customValidatorParams['customField'];
		$params = $customValidatorParams['params'];
		
		// Get CustomField column names
		$dataTypeColumnNames = CustomField::getDataTypeColumnNames();

		// get the target attribute
		$targetAttribute = $dataTypeColumnNames[$customField->data_type];

// TODO: this switch related to CustomFieldWidgets switch - possible call for sub classes - perhaps CustomValue should be abstract?

		// custom validation error message
		$validation_error = empty($customField->validation_error) ? array() : array('message'=>$customField->validation_error);

		// add any necassary validation rules to the model
		switch($customField->validation_type)
		{
			// Value list
			case CustomField::validation_typeValueList :
				$this->customValidators[] = array($targetAttribute, 'in', 'range'=>explode(',', $customField->validation_text)) + $validation_error;
				break;

			// Perl compatible regular expression
			case CustomField::validation_typePCRE :
				$this->customValidators[] = array($targetAttribute, 'match', 'pattern'=>$customField->validation_text) + $validation_error;
				break;

			// Numeric range
			case CustomField::validation_typeRange :
				$range = explode('-', $customField->validation_text);
				$this->customValidators[] = array($targetAttribute, 'numerical', 'min'=>$range[0], 'max'=>$range[1]) + $validation_error;
				break;

			// SQL select
			case CustomField::validation_typeSQLSelect:
				$this->customValidators[] = array($targetAttribute, 'validationLookup') + $validation_error + $params;
				break;
		}

		// mandatory
		if($customField->mandatory)
		{
			$this->customValidators[] = array($targetAttribute, 'required');
		}

		// force a re-read of validators
		$this->getValidators(NULL, TRUE);
	}

	/**
	 * This method shouldn't be necassary but here for security. This stops evil system admin injection
	 * and evil posting and tiny possibility of list changing while data entry occurring. Unlikely on all accounts.
	 * @param string $attribute the name of the attribute to be validated
	 * @param array $params options specified in the validation rule
	 */
	public function validationLookup($attribute, $params)
	{
		eval('$customField = $this->'.$params['relationToCustomField'].';');
		$this->checkLookup(
			$customField,
			$this->$attribute,
			$attribute
		);
	}

	public function checkLookup($customField, $value, $attribute)
	{
//TODO: open another database connection as this user whenever entering user entered sql.
//otherwise they can run their sql with full application access rights
		// test if sql is valid
		try
		{
			$sql = $customField->validation_text;
			if(!$row = Yii::app()->db->createCommand($sql)->queryRow())
			{
				$errorMessage = 'No rows in list - please contact the system administrator.';
			}
			else
			{
				// get name of first column which is our bound column
				$firstColumnName = each($row);
				$firstColumnName = $firstColumnName[0];
				$secondColumnName = ($secondColumnName = each($row)) ? $secondColumnName[0] : $firstColumnName;

				// test to see if the users value still exists in the list - in case of unlikely hacking of $_POST
				$command = Yii::app()->db->createCommand("SELECT `$secondColumnName` FROM ($sql) alias1 WHERE `$firstColumnName` = :$firstColumnName");
				$command->bindParam(":$firstColumnName", $value, PDO::PARAM_STR);
				// if no match
				if(($display = $command->queryScalar()) === false)
				{
					// if allowing new entries
					if($customField->allow_new)
					{
						$display = $value;
					}
					// otherwise there is an error
					else
					{
						$errorMessage = 'No match in list - please contact the system administrator.';
					}
				}
			}
		}
		catch(Exception $e)
		{
			$errorMessage = 'There is an error in the setup - please contact the system administrator, the database says:<br> '.$e->getMessage();
		}

		// if validation failed
		if($errorMessage)
		{
			$this->addError($attribute, $errorMessage);
		}

		return $display;
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
            'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'custom_value_id'),
            'projectToCustomFieldToProjectTemplates' => array(self::HAS_MANY, 'ProjectToCustomFieldToProjectTemplate', 'custom_value_id'),
            'taskToCustomFieldToTaskTemplates' => array(self::HAS_MANY, 'TaskToCustomFieldToTaskTemplate', 'custom_value_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'type_int' => 'Int',
			'type_float' => 'Float',
			'type_time' => 'Time',
			'type_date' => 'Date',
			'type_text' => 'Text',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.type_int',$this->type_int);
		$criteria->compare('t.type_float',$this->type_float);
		$criteria->compare('t.type_time',$this->type_time);
		$criteria->compare('t.type_date',Yii::app()->format->toMysqlDate($this->type_date));
		$criteria->compare('t.type_text',$this->type_text,true);

		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.type_int',
			't.type_float',
			't.type_time',
			't.type_date',
			't.type_text',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'type_int';
		$columns[] = 'type_float';
		$columns[] = 'type_time';
		$columns[] = 'type_date';
		$columns[] = 'type_text';

		return $columns;
	}

	public function getAttributeLabel($attribute) {
		return $this->label;
	}
	
	/*
	 * Set u5ser defined default
	 */
	public function setDefault(CActiveRecord $customField)
	{
		$dataTypeColumnNames = CustomField::getDataTypeColumnNames();
		$attributeName = $dataTypeColumnNames[$customField->data_type];

		// if this is likely to be an sql select
		if(stripos($customField->default_select, 'SELECT') !== false)
		{
			// attempt to execute the sql
			try
			{
// TODO: this should be run of connection with restricted sys admin rights rather than main app user rights
				$this->$attributeName = Yii::app()->db->createCommand($customField->default_select)->queryScalar();
			}
			catch (CDbException $e)
			{
				// the select failed so assume it is just text with the word 'select' in it - most likely sys admin error but
				// deal with it anyway by just doing nothing here and the attribute gets set below anyway
			}
		}
		else
		{
			// set to the value of the select column
			$this->$attributeName = $customField->default_select;
		}
	}

	// create a new customValue item
   // factory method for creating due to can't change method signature of derived class constructor
	static function createCustomField(&$CustomFieldModelTemplate, &$models, &$customValue)
	{
		// create the object
		$customValue = new self;

		// massive assignement - if created dynamically previously and now wanting to save/create
		if(isset($_POST['CustomValue']))
		{
			// this is a little dirty. Dealing with elements in order they were created as the id looses significance
			// as was dynamically created. An alternative would be for CustomValue id's to actually be the generic type id instead
			$customValue->attributes=array_shift($_POST['CustomValue']);
		}
		else
		{
			// set default value
			$customValue->setDefault($CustomFieldModelTemplate->customField);
		}
		
		// set label and id
		$customValue->setLabelAndId($CustomFieldModelTemplate);
		
		// attempt save
		$saved = $customValue->dbCallback('save');

		// record any errors
		$models[] = $customValue;

		return $saved;

	}

	public function setLabelAndId($CustomFieldModelTemplate)
	{
		$customField = $CustomFieldModelTemplate->customField;
		// Get CustomField column names
		$dataTypeColumnNames = CustomField::getDataTypeColumnNames();
		// label
		$this->label = $customField->description;
		// id
		$this->html_id = "CustomValue_{$CustomFieldModelTemplate->id}_{$dataTypeColumnNames[$customField->data_type]}";
	}

	
	public function getHtmlId($attribute)
	{
		return $this->html_id;
	}

}

?>