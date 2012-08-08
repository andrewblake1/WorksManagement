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
				$saved &= $this->addGenerics($model, $models);
			}

			// if all saved succesfully
			// NB: it is no good doing try and catch here as caught and dealt to error
			// in dbCallback
			if($saved)
			{
				// commit
                $transaction->commit();
				$this->redirect(array('admin','id'=>$model->getPrimaryKey()));
			}
			// otherwise there has been an error which should be captured in model
			else
			{
				// rollback
                $transaction->rollBack();
				$model->isNewRecord = TRUE;
			}
		}

		$this->breadcrumbs = $this->getBreadCrumbTrail('Create');

		$this->render('create',array(
			'model'=>$model,
			'models'=>$models,
		));
	}

	/**
	 * Creates the rows needed for generisizm.
	 * @param CActiveRecord $project the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function addGenerics($project, &$models=array())
	{
		$models[] = $project;
		// loop thru all generic project types associated to this models project type
		foreach($project->projectType->genericProjectTypes as $genericProjectType)
		{
			// create a new generic item to hold value
			$generic = new Generic();
			$saved = $generic->dbCallback('save');
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

}
