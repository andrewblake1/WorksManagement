<?php

class DutyController extends Controller
{

	/*
	 * overidden as because generic possibly added and want to edit after creation
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
		$saved = true;

		// if we need to create a generic
		if(!empty($model->taskTypeToDutyType->dutyType->generic_type_id))
		{
			// create a new generic item to hold value
			$saved &= Generic::createGeneric($model->taskTypeToDutyType->dutyType->genericType, $models, $generic);
			// associate the new generic to this duty
			$model->generic_id = $generic->id;
		}
		
		return $saved & parent::createSave($model, $models);
	}

	/*
	 * overidden as mulitple models
	 */
	protected function updateSave($model,  &$models=array())
	{
		$generic = $model->generic;

		// if we need to update a generic
		if(!empty($model->taskTypeToDutyType->dutyType->generic_type_id))
		{
			// massive assignement
			$generic->attributes=$_POST['Generic'][$generic->id];

			// set Generic custom validators as per the associated generic type
			$generic->setCustomValidators($model->taskTypeToDutyType->dutyType->genericType,
				array(
					'relationToGenericType'=>'duty->taskTypeToDutyType->dutyType->genericType',
				)
			);

			$saved &= parent::updateSave($generic, $models);
		}
		
		return $saved & parent::updateSave($model, $models);
	}

}

?>