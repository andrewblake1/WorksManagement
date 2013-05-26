<?php

abstract class CustomFieldExtensionActiveRecord extends ActiveRecord
{
	protected $classModelToCustomFieldModelTemplate;
	protected $attributeCustomFieldModelTemplate_id;
	protected $attributeModelId;
	protected $relationCustomFieldModelTemplates;
	protected $relationCustomFieldModelTemplate;
	protected $relationModelTemplate;
	protected $relationModelToCustomFieldModelTemplates;
	protected $relationModelToCustomFieldModelTemplate;
	
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
		foreach($this->{$this->relationModelTemplate}->{$this->relationCustomFieldModelTemplates} as $CustomFieldModelTemplate)
		{
			// create a new customValue item to hold value
			if($saved &= CustomValue::createCustomField($CustomFieldModelTemplate, $models, $customValue))
			{
				// create new modelToCustomFieldModelTemplate
				$modelToCustomFieldModelTemplate = new $this->classModelToCustomFieldModelTemplate();
				$modelToCustomFieldModelTemplate->{$this->attributeCustomFieldModelTemplate_id} = $CustomFieldModelTemplate->id;
				$modelToCustomFieldModelTemplate->{$this->attributeModelId} = $this->id;
				$modelToCustomFieldModelTemplate->custom_value_id = $customValue->id;
				// attempt save
				$saved &= $modelToCustomFieldModelTemplate->dbCallback('save');
				// record any errors
				$models[] = $modelToCustomFieldModelTemplate;
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
		foreach($this->{$this->relationModelToCustomFieldModelTemplates} as $modelToCustomFieldModelTemplate)
		{
			$customValue = $modelToCustomFieldModelTemplate->customValue;
			$CustomFieldModelTemplate = $modelToCustomFieldModelTemplate->{$this->relationCustomFieldModelTemplate};
			$customValue->setLabelAndId($CustomFieldModelTemplate);
			
			// massive assignement
			$customValue->attributes=$_POST['CustomValue'][$CustomFieldModelTemplate->id];

			// validate and save
			$saved &= $customValue->updateSave($models/*, array(
				'customField' => $modelToCustomFieldModelTemplate->{$this->relationCustomFieldModelTemplate}->customField,
				'params' => array(
					'relationModelToCustomFieldModelTemplate'=>$this->relationModelToCustomFieldModelTemplate,
					'relationCustomFieldModelTemplate'=>$this->relationCustomFieldModelTemplate,
				),
			)*/);
			{//<input id="CustomField_2_type_int" class="span5" type="text" name="CustomValue[2][type_int]">
				$t = $customValue->getErrors();
			}
		}

		return $saved;
	}

/*	protected function getHtmlId($attribute)
	{
		if(($modelName = get_class($this)) == 'customValue')
		{
			return "{$modelName_}{$model->primaryKey}_$attribute";
		}
		
		return parent::getHtmlId($attribute);
	}*/

}
?>