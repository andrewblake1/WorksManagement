<?php

class CrewController extends Controller
{

	/*
	 * overidden as mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		// not using this so able to make this static as needed in TaskController
		return static::createSaveStatic($model, $models);
	}

	static function createSaveStatic($model, &$models=array())
	{
		// need to insert a row into the planning nested set model so that the id can be used here
		
		// create a root node
		$planning = new Planning;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		if($saved = $planning->appendTo(Planning::model()->findByPk($model->day_id)))
		{
			$model->id = $planning->id;
			$saved = parent::createSave($model, $models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model,  &$models=array())
	{
		// get the planning model
		$planning = Planning::model()->findByPk($model->id);
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		// atempt save
		$saved = $planning->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved & parent::updateSave($model, $models);
	}


}

?>