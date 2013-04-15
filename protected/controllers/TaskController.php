<?php
class TaskController extends GenericExtensionController
{
	protected $class_ModelToGenericModelType = 'TaskToGenericTaskType';
	protected $attribute_generic_model_type_id = 'generic_task_type_id';
	protected $attribute_model_id = 'task_id';
	protected $relation_genericModelType = 'genericTaskType';
	protected $relation_genericModelTypes = 'genericTaskTypes';
	protected $relation_modelType = 'taskType';
	protected $relation_modelToGenericModelTypes = 'taskToGenericTaskTypes';
	protected $relation_modelToGenericModelType = 'taskToGenericTaskType';

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

		if($saved = $planning->appendTo(Planning::model()->findByPk($model->crew_id)))
		{
			$model->id = $planning->id;
			// parent create save will add generics -- all we need to do is take care care of adding the other things if no errors
			// NB: by calling the parent the $model model is added into $models
			if($saved = parent::createSave($model, $models))
			{
				// attempt creation of resources
				$saved &= $this->createResources($model, $models);
				// attempt creation of assemblies
				$saved &= $this->createAssemblies($model, $models);
				// attempt creation of materials
				$saved &= $this->createMaterials($model, $models);
				// attempt creation of duties
				$saved &= $this->createDutys($model, $models);
			}
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
		$planning->name = $model->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		// atempt save
		$saved = $planning->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved & parent::updateSave($model, $models);
	}


// TODO: replace these with trigger after insert on model. Also cascade delete on these 3 tables
// Also update triggers possibly to maintain ref integ. easiest for now in application code but not great for integrity.
	
	/**
	 * Creates the intial resource rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createResources($task, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($task->taskType->taskTypeToResourceTypes as $taskTypeToResourceType)
		{
			// create a new resource
			$model = new TaskToResourceType();
			// copy any useful attributes from
			$model->attributes = $taskTypeToResourceType->attributes;
			$model->staff_id = null;
			$model->task_id = $task->id;
//			$model->resource_type_id = $taskTypeToResourceType->resource_type_id;
			$saved &= TaskToResourceTypeController::createSaveStatic($model, $models, $taskTypeToResourceType);
		}
		
		return $saved;
	}

	/**
	 * Append assemblies to task.
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createAssemblies($task, &$models=array())
	{
		// initialise the saved variable to show no errors
		$saved = true;
		
		// loop thru all all assemblies related to the tasks type
		foreach($task->taskType->taskTypeToAssemblies as $taskTypeToAssembly)
		{
			$saved = TaskToAssemblyController::addAssembly($task->id, $taskTypeToAssembly->assembly_id, $taskTypeToAssembly->quantity, null, $models);
		}
		
		return $saved;
	} 
	
	/**
	 * Creates the intial material rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createMaterials($task, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($task->taskType->taskTypeToMaterials as $taskTypeToMaterial)
		{
			// create a new materials
			$model = new TaskToMaterial();
			// copy any useful attributes from
			$model->attributes = $taskTypeToMaterial->attributes;
			$model->staff_id = null;
			$model->task_id = $task->id;
			// need dummy store id to get around rules
			$model->store_id = 0;
			$saved &= $model->dbCallback('save');
			$models[] = $model;
		}
		
		return $saved;
	}

	/**
	 * Creates the intial duty rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createDutys($task, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($task->taskType->taskTypeToDutyTypes as $taskTypeToDutyType)
		{
			// create a new duty
			$model = new Duty();
			// copy any useful attributes from
			$model->attributes = $taskTypeToDutyType->attributes;
			$model->staff_id = null;
			$model->task_id = $task->id;
			$saved &= DutyController::createSaveStatic($model, $models);
		}
		
		return $saved;
	}

}

?>