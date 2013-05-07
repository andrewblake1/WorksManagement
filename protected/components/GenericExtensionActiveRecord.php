<?php

abstract class GenericExtensionActiveRecord extends ActiveRecord
{
	protected $class_ModelToGenericModelType;
	protected $attribute_generic_model_type_id;
	protected $attribute_model_id;
	protected $relation_genericModelTypes;
	protected $relation_genericModelType;
	protected $relation_modelType;
	protected $relation_modelToGenericModelTypes;
	protected $relation_modelToGenericModelType;
	
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		if($saved = $this->dbCallback('save'))
		{
			// attempt creation of generics
			$saved &= $this->createGenerics($models);
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
			// attempt creation of generics
			$saved &= $this->updateGenerics($models);
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
	protected function createGenerics(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($this->{$this->relation_modelType}->{$this->relation_genericModelTypes} as $genericModelType)
		{
			// create a new generic item to hold value
//			$saved &= Generic::createGeneric($genericModelType->genericType, $models, $generic, $genericModelType);
			// validate and save
			$generic = new Generic();
			// massive assignement - if created dynamically previously and now wanting to save/create
			if(isset($_POST['Generic']))
			{
				if(isset($_POST['Generic'][$genericModelType->id]))
				{
					$generic->attributes=$_POST['Generic'][$genericModelType->id];
				}
			}
			else
			{
				// set default value
				$generic->setDefault($genericModelType->genericType);
			}
			
//			$generic->label = $genericModelType->genericType->description;
			$generic->setLabelAndId($genericModelType);
			if($saved &= $generic->createSave($models/*, array(
				'genericType' => $genericModelType->genericType,
				'params' => array(
					'relation_modelToGenericModelType'=>$this->relation_modelToGenericModelType,
					'relation_genericModelType'=>$this->relation_genericModelType,
				),
			)*/))
			{
				// create new modelToGenericModelType
				$modelToGenericModelType = new $this->class_ModelToGenericModelType();
				$modelToGenericModelType->{$this->attribute_generic_model_type_id} = $genericModelType->id;
				$modelToGenericModelType->{$this->attribute_model_id} = $this->id;
				$modelToGenericModelType->generic_id = $generic->id;
				// attempt save
				$saved &= $modelToGenericModelType->dbCallback('save');
				// record any errors
				$models[] = $modelToGenericModelType;
			}
			else
			{//<input id="Generic_2_type_int" class="span5" type="text" name="Generic[2][type_int]">
				$t = $generic->getErrors();
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
	private function updateGenerics(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($this->{$this->relation_modelToGenericModelTypes} as $modelToGenericModelType)
		{
			$generic = $modelToGenericModelType->generic;
			$genericModelType = $modelToGenericModelType->{$this->relation_genericModelType};
			$generic->setLabelAndId($genericModelType);
			
			// massive assignement
			$generic->attributes=$_POST['Generic'][$genericModelType->id];

			// validate and save
			$saved &= $generic->updateSave($models/*, array(
				'genericType' => $modelToGenericModelType->{$this->relation_genericModelType}->genericType,
				'params' => array(
					'relation_modelToGenericModelType'=>$this->relation_modelToGenericModelType,
					'relation_genericModelType'=>$this->relation_genericModelType,
				),
			)*/);
			{//<input id="Generic_2_type_int" class="span5" type="text" name="Generic[2][type_int]">
				$t = $generic->getErrors();
			}
		}

		return $saved;
	}

/*	protected function getHtmlId($attribute)
	{
		if(($modelName = get_class($this)) == 'Generic')
		{
			return "{$modelName_}{$model->primaryKey}_$attribute";
		}
		
		return parent::getHtmlId($attribute);
	}*/

}
?>