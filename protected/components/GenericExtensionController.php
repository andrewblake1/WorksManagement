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
	
	public function actionCreate()
	{
		$model=new $this->modelName;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			
			// start a transaction
			$transaction = Yii::app()->db->beginTransaction();
			
			// attempt save of this model without redirect
			if($saved = $model->dbCallback('save'))
			{
				// attempt creation of generics
				$saved &= $this->createGenerics($model, $models);
			}

			// if all saved succesfully
			// NB: it is no good doing try and catch here as caught and dealt to error
			// in dbCallback
			if($saved)
			{
				// commit
                $transaction->commit();
				$this->redirect(array('update', 'id'=>$model->getPrimaryKey()));
			}
			// otherwise there has been an error which should be captured in model
			else
			{
				// rollback
                $transaction->rollBack();
				$model->isNewRecord = TRUE;
			}
		}

		$this->widget('CreateViewWidget', array(
			'model'=>$model,
			'models'=>$models,
		));
	}

	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			
			// start a transaction
			$transaction = Yii::app()->db->beginTransaction();
			
			// attempt save of this model without redirect
			if($saved = $model->dbCallback('save'))
			{
				// attempt update of generics
				$saved &= $this->updateGenerics($model, $models);
			}

			// if all saved succesfully
			// NB: it is no good doing try and catch here as caught and dealt to error
			// in dbCallback
			if($saved)
			{
				// commit
                $transaction->commit();
				$this->redirect(array('admin'));
			}
			// otherwise there has been an error which should be captured in model
			else
			{
				// rollback
                $transaction->rollBack();
			}
		}

		$this->widget('UpdateViewWidget', array(
			'model'=>$model,
			'models'=>$models,
		));
	}

// TODO: replace with trigger after insert on model. Also cascade delete on these 3 tables
// Also update triggers possibly to maintain ref integ. easiest for now in application code.
	/**
	 * Creates the rows needed for generisizm.
	 * @param CActiveRecord $model the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createGenerics($model, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// put the model into the models array used for showing all errors
		$models[] = $model;
		
		// loop thru all generic model types associated to this models model type
		foreach($model->{$this->relation_modelType}->{$this->relation_genericModelTypes} as $genericModelType)
		{
			// create a new generic item to hold value
			$generic = new Generic();
			$saved &= $generic->dbCallback('save');
			$models[] = $generic;
			// create new modelToGenericModelType
			$modelToGenericModelType = new $this->class_ModelToGenericModelType();
			$modelToGenericModelType->{$this->attribute_generic_model_type_id} = $genericModelType->id;
			$modelToGenericModelType->{$this->attribute_model_id} = $model->id;
			$modelToGenericModelType->generic_id = $generic->id;
			$saved &= $modelToGenericModelType->dbCallback('save');
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
		
		// put the model into the models array used for showing all errors
		$models[] = $model;
		 
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

}
?>