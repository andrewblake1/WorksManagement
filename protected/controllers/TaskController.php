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
	 * overidden as because generics added want to edit them after creation
	 */
	protected function createRedirect($model)
	{
		$this->redirect(array('update', 'id'=>$model->getPrimaryKey()));
	}

	/*
	 * overidden as mulitple models
	 */
	protected function createSave($task, &$models=array())
	{
		// parent create save will add generics -- all we need to do is take care care of adding the other things if no errors
		// NB: by calling the parent the $task model is added into $models
		if($saved = parent::createSave($task, $models))
		{
			// attempt creation of resources
			$saved &= $this->createResources($task, $models);
			// attempt creation of assemblies
			$saved &= $this->createAssemblys($task, $models);
			// attempt creation of assemblies
			$saved &= $this->createMaterials($task, $models);
			// attempt creation of assemblies
			$saved &= $this->createDutys($task, $models);
		}
		
		return $saved;
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
			$saved &= $model->dbCallback('save');
			$models[] = $model;
		}
		
		return $saved;
	}

	/**
	 * Creates the intial assembly rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createAssemblys($task, &$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model generics - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all generic model types associated to this models model type
		foreach($task->taskType->taskTypeToAssemblies as $taskTypeToAssembly)
		{
			// create a new assemblys
			$model = new TaskToAssembly();
			// copy any useful attributes from
			$model->attributes = $taskTypeToAssembly->attributes;
			$model->staff_id = null;
			$model->task_id = $task->id;
			$saved &= $model->dbCallback('save');
			$models[] = $model;
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
			$model = new MaterialToTask();
			// copy any useful attributes from
			$model->attributes = $taskTypeToMaterial->attributes;
			$model->staff_id = null;
			$model->task_id = $task->id;
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
			$model->task_type_to_duty_type_id = $taskTypeToDutyType->id;
			// if we need to create a generic
			if(!empty($taskTypeToDutyType->dutyType->generic_type_id))
			{
				// create a new generic item to hold value
				$saved &= Generic::createGeneric($taskTypeToDutyType->dutyType->genericType, $models, $generic);
				// associate the new generic to this duty
				$model->generic_id = $generic->id;
			}
			// attempt save
			$saved &= $model->dbCallback('save');
			// record any errors
			$models[] = $model;
		}
		
		return $saved;
	}

}

?>