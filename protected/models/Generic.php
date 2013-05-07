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
 * @property Duty $duty
 * @property Staff $staff
 * @property ProjectToGenericProjectType $projectToGenericProjectType
 * @property TaskToGenericTaskType $taskToGenericTaskType
 */
class Generic extends ActiveRecord
{
	public $label = '';
	public $htmlId;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return $this->customValidators + array(
			array('type_int', 'numerical', 'integerOnly'=>true),
			array('type_float', 'numerical'),
			array('type_text', 'length', 'max'=>255),
			array('type_time, type_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type_int, type_float, type_time, type_date, type_text', 'safe', 'on'=>'search'),
		);
	}

	public function setCustomValidators($customValidatorParams = array())
	{
		$genericType = $customValidatorParams['genericType'];
		$params = $customValidatorParams['params'];
		
		// Get GenericType column names
		$dataTypeColumnNames = GenericType::getDataTypeColumnNames();

		// get the target attribute
		$targetAttribute = $dataTypeColumnNames[$genericType->data_type];

// TODO: this switch related to GenericWidgets switch - possible call for sub classes - perhaps Generic should be abstract?

		// custom validation error message
		$validationError = empty($genericType->validation_error) ? array() : array('message'=>$genericType->validation_error);

		// add any necassary validation rules to the model
		switch($genericType->validation_type)
		{
			// Value list
			case GenericType::validationTypeValueList :
				$this->customValidators[] = array($targetAttribute, 'in', 'range'=>explode(',', $genericType->validation_text)) + $validationError;
				break;

			// Perl compatible regular expression
			case GenericType::validationTypePCRE :
				$this->customValidators[] = array($targetAttribute, 'match', 'pattern'=>$genericType->validation_text) + $validationError;
				break;

			// Numeric range
			case GenericType::validationTypeRange :
				$range = explode('-', $genericType->validation_text);
				$this->customValidators[] = array($targetAttribute, 'numerical', 'min'=>$range[0], 'max'=>$range[1]) + $validationError;
				break;

			// SQL select
			case GenericType::validationTypeSQLSelect:
				$this->customValidators[] = array($targetAttribute, 'validationLookup') + $validationError + $params;
				break;
		}

		// mandatory
		if($genericType->mandatory)
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
		eval('$genericType = $this->'.$params['relationToGenericType'].';');
		$this->checkLookup(
			$genericType,
			$this->$attribute,
			$attribute
		);
	}

	public function checkLookup($genericType, $value, $attribute)
	{
//TODO: open another database connection as this user whenever entering user entered sql.
//otherwise they can run their sql with full application access rights
		// test if sql is valid
		try
		{
			$sql = $genericType->validation_text;
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
					if($genericType->allow_new)
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
			'duty' => array(self::HAS_ONE, 'Duty', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToGenericProjectType' => array(self::HAS_ONE, 'ProjectToGenericProjectType', 'generic_id'),
			'taskToGenericTaskType' => array(self::HAS_ONE, 'TaskToGenericTaskType', 'generic_id'),
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
	public function setDefault(CActiveRecord $genericType)
	{
		$dataTypeColumnNames = GenericType::getDataTypeColumnNames();
		$attributeName = $dataTypeColumnNames[$genericType->data_type];

		// if this is likely to be an sql select
		if(stripos($genericType->default_select, 'SELECT') !== false)
		{
			// attempt to execute the sql
			try
			{
// TODO: this should be run of connection with restricted sys admin rights rather than main app user rights
				$this->$attributeName = Yii::app()->db->createCommand($genericType->default_select)->queryScalar();
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
			$this->$attributeName = $genericType->default_select;
		}
	}

	// create a new generic item
   // factory method for creating due to can't change method signature of derived class constructor
	static function createGeneric(&$genericModelType, &$models, &$generic)
	{
		// create the object
		$generic = new self;

		// massive assignement - if created dynamically previously and now wanting to save/create
		if(isset($_POST['Generic'][$genericModelType->id]))
		{
			$generic->attributes=$_POST['Generic'][$genericModelType->id];
		}
		else
		{
			// set default value
			$generic->setDefault($genericModelType->genericType);
		}
		
		// set label and id
		$generic->setLabelAndId($genericModelType);
		
		// attempt save
		$saved = $generic->dbCallback('save');

		// record any errors
		$models[] = $generic;

		return $saved;

	}

	public function setLabelAndId($genericModelType)
	{
		$genericType = $genericModelType->genericType;
		// Get GenericType column names
		$dataTypeColumnNames = GenericType::getDataTypeColumnNames();
		// label
		$this->label = $genericType->description;
		// id
		$this->htmlId = "Generic_{$genericModelType->id}_{$dataTypeColumnNames[$genericType->data_type]}";
	}

	
	public function getHtmlId($attribute)
	{
		return $this->htmlId;
	}

}

?>