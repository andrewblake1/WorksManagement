<?php
class ProjectController extends GenericExtensionController
{
	protected $class_ModelToGenericModelType = 'ProjectToGenericProjectType';
	protected $attribute_generic_model_type_id = 'generic_project_type_id';
	protected $attribute_model_id = 'project_id';
	protected $relation_genericModelType = 'genericProjectType';
	protected $relation_genericModelTypes = 'genericProjectTypes';
	protected $relation_modelType = 'projectType';
	protected $relation_modelToGenericModelTypes = 'projectToGenericProjectTypes';
	protected $relation_modelToGenericModelType = 'projectToGenericProjectType';

	/*
	 * overidden as because generics added want to edit them after creation
	 */
	protected function createRedirect($model)
	{
		$this->redirect(array('update', 'id'=>$model->getPrimaryKey()));
	}

	/*
	 * overidden as mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		// need to insert a row into the planning nested set model so that the id can be used here

		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$planning = new Planning;
		$planning->name = $model->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		if($saved = $planning->saveNode(true))
		{
			// add the Project
			$model->id = $planning->id;
			$saved = parent::createSave($model, $models);

			// add a Day
			$day = new Day;
			$day->project_id = $model->id;
			$saved = DayController::createSaveStatic($day, $models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;

		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model, &$models=array())
	{
		// get the planning model
		$planning = Planning::model()->findByPk($model->id);
		$planning->name = $model->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		// atempt save
		$saved = $planning->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $planning;

		return $saved & parent::updateSave($model, $models);
	}

	public function actionUpdate($id) {
		// ensure access
		if(!Yii::app()->user->checkAccess('Project', array('primaryKey'=>$id)))
		{
			throw new CHttpException(403);
		}
		
		parent::actionUpdate($id);
	}

	public function actionDelete($id) {
		// ensure access
		if(!Yii::app()->user->checkAccess('Project', array('primaryKey'=>$id)))
		{
			throw new CHttpException(403);
		}
		
		parent::actionDelete($id);
	}

}

?>