<?php

class ProjectController extends Controller
{

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

// TODO: replace with trigger after insert on project. Also cascade delete on these 3 tables
// Also update triggers possibly to maintain ref integ. easiest for now in application code.
	/**
	 * Creates the rows needed for generisizm.
	 * @param CActiveRecord $project the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createGenerics($project, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// project generics - otherwise will return null indicating a save error
		$saved = true;
		
		// put the project into the models array used for showing all errors
		$models[] = $project;
		
		// loop thru all generic project types associated to this models project type
		foreach($project->projectType->genericProjectTypes as $genericProjectType)
		{
			// create a new generic item to hold value
			$generic = new Generic();
			$saved &= $generic->dbCallback('save');
			$models[] = $generic;
			// create new ProjectToGenericProjectType
			$projectToGenericProjectType = new ProjectToGenericProjectType();
			$projectToGenericProjectType->generic_project_type_id = $genericProjectType->id;
			$projectToGenericProjectType->project_id = $project->id;
			$projectToGenericProjectType->generic_id = $generic->id;
			$saved &= $projectToGenericProjectType->dbCallback('save');
			$models[] = $projectToGenericProjectType;
		}
		
		return $saved;
	}

	/**
	 * Updates the rows needed for generisizm.
	 * @param CActiveRecord $project the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function updateGenerics($project, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// project generics - otherwise will return null indicating a save error
		$saved = true;
		
		// put the project into the models array used for showing all errors
		$models[] = $project;
		
		// loop thru all generic project types associated to this models project type
		foreach($project->projectToGenericProjectTypes as $projectToGenericProjectType)
		{
			$generic = $projectToGenericProjectType->generic;
			
			// massive assignement
			$generic->attributes=$_POST['Generic'][$generic->id];
			
			// set Generic custom validators as per the associated generic type
			$generic->setCustomValidators($projectToGenericProjectType->genericProjectType->genericType);

			// attempt to save
			$saved &= $generic->dbCallback('save');
			
			// collect model for possible error extraction
			$models[] = $generic;
		}
		
		return $saved;
	}

}
