<?php

class CustomValueActiveRecord extends ActiveRecord
{
	protected $customValidatorParams = array();

	public function setCustomValidators()
	{

		$customField = $this->customValidatorParams['customField'];
		$params = $this->customValidatorParams['params'];
		
		// custom validation error message
		$validation_error = empty($customField->validation_error) ? array() : array('message'=>$customField->validation_error);

		// add any necassary validation rules to the model
		switch($customField->validation_type)
		{
			// Value list
			case CustomField::validation_typeValueList :
				$this->customValidators[] = array('custom_value', 'in', 'range'=>explode(',', $customField->validation_text)) + $validation_error;
				break;

			// Perl compatible regular expression
			case CustomField::validation_typePCRE :
				$this->customValidators[] = array('custom_value', 'match', 'pattern'=>$customField->validation_text) + $validation_error;
				break;

			// Numeric range
			case CustomField::validation_typeRange :
				$range = explode('-', $customField->validation_text);
				$this->customValidators[] = array('custom_value', 'numerical', 'min'=>$range[0], 'max'=>$range[1]) + $validation_error;
				break;

			// SQL select
			case CustomField::validation_typeSQLSelect:
				$this->customValidators[] = array('custom_value', 'validationLookup') + $validation_error + $params;
				break;
		}

		// mandatory
		if($customField->mandatory)
		{
			$this->customValidators[] = array('custom_value', 'required');
		}

		parent::setCustomValidators();
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

	/*
	 * Set user defined default
	 */
	public function setDefault(CActiveRecord $customField)
	{
		// if this is likely to be an sql select
		if(stripos($customField->default_select, 'SELECT') !== false)
		{
			// attempt to execute the sql
			try
			{
// TODO: this should be run of connection with restricted sys admin rights rather than main app user rights
				$this->custom_value = Yii::app()->db->createCommand($customField->default_select)->queryScalar();
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
			$this->custom_value = $customField->default_select;
		}
	}
	
}

?>