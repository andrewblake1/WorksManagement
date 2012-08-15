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
 * @property ProjectToGenericProjectType $projectToGenericProjectType
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 */
class Generic extends ActiveRecord
{
	/* 
	 * array of validation rules appended to rules at run time as determined
	 * by the related GenericType
	 */
	public $customValidators = array();

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
		return $this->customValidators + array(
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
	
	public function setCustomValidators($genericType, $params)
	{
		// Get GenericType column names
		$dataTypeColumnNames = GenericType::getDataTypeColumnNames();
		
		// get the target attribute
		$targetAttribute = $dataTypeColumnNames[$genericType->data_type];

		// add any necassary validation rules to the model
		switch($genericType->validation_type)
		{
			// Value list
			case GenericType::validationTypeValueList :
				$this->customValidators[] = array($targetAttribute, 'in', 'range'=>explode(',', $genericType->validation_text));
				break;

			// Perl compatible regular expression
			case GenericType::validationTypePCRE :
				$this->customValidators[] = array($targetAttribute, 'match', 'pattern'=>$genericType->validation_text);
				break;

			// Numeric range
			case GenericType::validationTypeRange :
				$range = explode('-', $genericType->validation_text);
				$this->customValidators[] = array($targetAttribute, 'numerical', 'min'=>$range[0], 'max'=>$range[1]);
				break;

			// SQL select
			case GenericType::validationTypeSQLSelect:
				$this->customValidators[] = array($targetAttribute, 'validationLookup') + $params;
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
	 * Override of this necassary because _validators is private var of CModel and populated
	 * on construct or sometime before our call to dynamically add validators.
	 */
	public function getValidators($attribute=null, $force=false)
	{
		static $_validators = NULL;

		if($force)
		{
			$_validators = $this->createValidators();
		}
		elseif($_validators === NULL)
		{
			$_validators = parent::getValidators($attribute);
		}
		
		$validators=array();
		$scenario=$this->getScenario();
		foreach($_validators as $validator)
		{
			if($validator->applyTo($scenario))
			{
				if($attribute===null || in_array($attribute, $validator->attributes,true))
					$validators[]=$validator;
			}
		}

		return $validators;
	}


	/**
	 * This method shouldn't be necassary but here for security. This stops evil system admin injection
	 * and evil posting and tiny possibility of list changing while data entry occurring. Unlikely on all accounts.
	 * @param string $attribute the name of the attribute to be validated
	 * @param array $params options specified in the validation rule
	 */
	public function validationLookup($attribute, $params)
	{
		$this->checkLookup(
			$this->$params['relation_modelToGenericModelType']->$params['relation_genericModelType']->genericType,
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
			'duties' => array(self::HAS_MANY, 'Duty', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToGenericProjectType' => array(self::HAS_ONE, 'ProjectToGenericProjectType', 'generic_id'),
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

		$criteria->compare('id',$this->id);
		$criteria->compare('type_int',$this->type_int);
		$criteria->compare('type_float',$this->type_float);
		$criteria->compare('type_time',$this->type_time);
		$criteria->compare('type_date',$this->type_date);
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