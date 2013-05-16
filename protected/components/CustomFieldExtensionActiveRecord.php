<?php

abstract class CustomFieldExtensionActiveRecord extends ActiveRecord
{
	protected $classModelToCustomFieldModelType;
	protected $attributeCustomFieldModelType_id;
	protected $attributeModel_id;
	protected $relationCustomFieldModelTypes;
	protected $relationCustomFieldModelType;
	protected $relationModelType;
	protected $relationModelToCustomFieldModelTypes;
	protected $relationModelToCustomFieldModelType;
	
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		if($saved = $this->dbCallback('save'))
		{
			// attempt creation of customValues
			$saved &= $this->createCustomFields($models);
		}
		else
		{
			// put the model into the models array used for showing all errors
			$models[] = $this;
		}
		
		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		if($saved = $this->dbCallback('save'))
		{
			// attempt creation of customValues
			$saved &= $this->updateCustomFields($models);
		}
		else
		{
			// put the model into the models array used for showing all errors
			$models[] = $this;
		}
		
		return $saved;
	}

// TODO: replace with trigger after insert on model. Also cascade delete on these 3 tables
// Also update triggers possibly to maintain ref integ. easiest for now in application code but not great for integrity.
	/**
	 * Creates the rows needed for generisizm.
	 * @param CActiveRecord $model the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	protected function createCustomFields(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all customValue model types associated to this models model type
		foreach($this->{$this->relationModelType}->{$this->relationCustomFieldModelTypes} as $CustomFieldModelType)
		{
			// create a new customValue item to hold value
			if($saved &= CustomValue::createCustomField($CustomFieldModelType, $models, $customValue))
			{
				// create new modelToCustomFieldModelType
				$modelToCustomFieldModelType = new $this->classModelToCustomFieldModelType();
				$modelToCustomFieldModelType->{$this->attributeCustomFieldModelType_id} = $CustomFieldModelType->id;
				$modelToCustomFieldModelType->{$this->attributeModel_id} = $this->id;
				$modelToCustomFieldModelType->custom_value_id = $customValue->id;
				// attempt save
				$saved &= $modelToCustomFieldModelType->dbCallback('save');
				// record any errors
				$models[] = $modelToCustomFieldModelType;
			}
			else
			{//<input id="CustomField_2_type_int" class="span5" type="text" name="CustomValue[2][type_int]">
				$t = $customValue->getErrors();
			}
		}
		
		return $saved;
	}

	/**
	 * Updates the rows needed for generisizm.
	 * @param CActiveRecord $model the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function updateCustomFields(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all customValue model types associated to this models model type
		foreach($this->{$this->relationModelToCustomFieldModelTypes} as $modelToCustomFieldModelType)
		{
			$customValue = $modelToCustomFieldModelType->customValue;
			$CustomFieldModelType = $modelToCustomFieldModelType->{$this->relationCustomFieldModelType};
			$customValue->setLabelAndId($CustomFieldModelType);
			
			// massive assignement
			$customValue->attributes=$_POST['CustomValue'][$CustomFieldModelType->id];

			// validate and save
			$saved &= $customValue->updateSave($models/*, array(
				'customField' => $modelToCustomFieldModelType->{$this->relationCustomFieldModelType}->customField,
				'params' => array(
					'relationModelToCustomFieldModelType'=>$this->relationModelToCustomFieldModelType,
					'relationCustomFieldModelType'=>$this->relationCustomFieldModelType,
				),
			)*/);
			{//<input id="CustomField_2_type_int" class="span5" type="text" name="CustomValue[2][type_int]">
				$t = $customValue->getErrors();
			}
		}

		return $saved;
	}

/*	protected function getHtml_id($attribute)
	{
		if(($modelName = get_class($this)) == 'customValue')
		{
			return "{$modelName_}{$model->primaryKey}_$attribute";
		}
		
		return parent::getHtml_id($attribute);
	}*/

}
?>