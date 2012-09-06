<?php

abstract class GenericExtensionController extends Controller
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
	protected function createSave($model,  &$models=array())
	{
		if($saved = $model->dbCallback('save'))
		{
			// attempt creation of generics
			$saved &= $this->createGenerics($model, $models);
		}
		else
		{
			// put the model into the models array used for showing all errors
			$models[] = $model;
		}
		
		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model,  &$models=array())
	{
		if($saved = $model->dbCallback('save'))
		{
			// attempt creation of generics
			$saved &= $this->updateGenerics($model, $models);
		}
		else
		{
			// put the model into the models array used for showing all errors
			$models[] = $model;
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
	protected function createGenerics($model, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($model->{$this->relation_modelType}->{$this->relation_genericModelTypes} as $genericModelType)
		{
			// create a new generic item to hold value
			$saved &= Generic::createGeneric($genericModelType->genericType, $models, $generic);
			// create new modelToGenericModelType
			$modelToGenericModelType = new $this->class_ModelToGenericModelType();
			$modelToGenericModelType->{$this->attribute_generic_model_type_id} = $genericModelType->id;
			$modelToGenericModelType->{$this->attribute_model_id} = $model->id;
			$modelToGenericModelType->generic_id = $generic->id;
			// attempt save
			$saved &= $modelToGenericModelType->dbCallback('save');
			// record any errors
			$models[] = $modelToGenericModelType;
		}
		
		return $saved;
	}

	/**
	 * Updates the rows needed for generisizm.
	 * @param CActiveRecord $model the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function updateGenerics($model, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($model->{$this->relation_modelToGenericModelTypes} as $modelToGenericModelType)
		{
			$generic = $modelToGenericModelType->generic;
			
			// massive assignement
			$generic->attributes=$_POST['Generic'][$generic->id];
			
			// set Generic custom validators as per the associated generic type
			$generic->setCustomValidators($modelToGenericModelType->{$this->relation_genericModelType}->genericType,
				array(
					'relation_modelToGenericModelType'=>$this->relation_modelToGenericModelType,
					'relation_genericModelType'=>$this->relation_genericModelType,

				)
			);

			// attempt to save
			$saved &= $generic->dbCallback('save');
			
			// collect model for possible error extraction
			$models[] = $generic;
		}

		return $saved;
	}

	protected function actionGetHtmlId($model,$attribute)
	{
		$modelName = get_class($model);
		
		if($modelName == 'Generic')
		{
			return get_class($model)."_{$model->primaryKey}_$attribute";
		}
		
		return parent::actionGetHtmlId($model,$attribute);
	}

}
?>