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
		// need to insert a row into the schedule nested set model so that the id can be used here

		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$schedule = new Schedule;
		$schedule->name = $model->name;
		$schedule->in_charge_id = empty($_POST['Schedule']['in_charge_id']) ? null : $_POST['Schedule']['in_charge_id'];
		if($saved = $schedule->saveNode(true))
		{
			// add the Project
			$model->id = $schedule->id;
			$saved = parent::createSave($model, $models);

			// add a Day
			$day = new Day;
			$day->project_id = $model->id;
			$saved = DayController::createSaveStatic($day, $models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $schedule;

		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model, &$models=array())
	{
		// get the schedule model
		$schedule = Schedule::model()->findByPk($model->id);
		$schedule->name = $model->name;
		$schedule->in_charge_id = empty($_POST['Schedule']['in_charge_id']) ? null : $_POST['Schedule']['in_charge_id'];
		// atempt save
		$saved = $schedule->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $schedule;

		return $saved & parent::createSave($model, $models);
	}

}

?>