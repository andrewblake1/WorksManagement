<?php

class TaskToAssemblyController extends AdjacencyListController
{
	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = 'ViewTaskToAssembly';

	public function setUpdateTabs($model)
	{
		// set up tab menu if required - using setter
		$this->tabs = $model;

		// add tab to standard drawings
		$this->addTab(AssemblyToStandardDrawing::getNiceNamePlural(), array(
			'AssemblyToStandardDrawing/admin',
			'assembly_id' => $model->assembly_id,
			));

		// add tab t o materials
		$this->addTab(Material::getNiceNamePlural(), array(
			'TaskToMaterial/admin',
			'task_to_assembly_id' => $_GET['id'],
			'task_id' => $model->task_id,
		));
	}
	
	/*
	 * to be overidden if using mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		for($cntr = 0; $cntr < $model->quantity; $cntr++)
		{
			$saved = static::addAssembly($model->task_id, $model->assembly_id, null, $models);
		}
		
		return $saved;
	}
	
	/**
	 * Recursive function to add assembly with sub assemblies to task. Inserts row into task_to_assembly and also appends materials to task_to_materials
	 * @param int $task_id the task id to add assembly to
	 * @param int $assembly_id the assembly id to add to task
	 * @param int $parent_id the id of the parent within this model - adjacency list
	 * @return returns 0, or null on error of any inserts
	 */
	static function addAssembly($task_id, $assembly_id, $parent_id = null, &$models=array())
	{
		// initialise the saved variable to show no errors
		$saved = true;
		
		// insert assembly into task_to_assembly table
		$taskToAssembly = new TaskToAssembly();
		$taskToAssembly->task_id = $task_id;
		$taskToAssembly->assembly_id = $assembly_id;
		$taskToAssembly->parent_id = $parent_id;
		$taskToAssembly->quantity = 1;	// dummy value as always 1 but quanity used on the create form and required
		$saved &= $taskToAssembly->dbCallback('save');
		$models[] = $taskToAssembly;
		
		// insert materials into task_to_material table
		
		// from AssemblyToMaterial
		foreach(AssemblyToMaterial::model()->findAllByAttributes(array('assembly_id'=>$assembly_id)) as $assemblyToMaterial)
		{
			$taskToMaterial = new TaskToMaterial();
			$taskToMaterial->task_id = $task_id;
			$taskToMaterial->material_id = $assemblyToMaterial->material_id;
			$taskToMaterial->task_to_assembly_id = $taskToAssembly->id;
			$taskToMaterial->store_id = $taskToAssembly->assembly->store_id;
			$taskToMaterial->quantity = $assemblyToMaterial->quantity;
			if($saved &= $taskToMaterial->dbCallback('save'))
			{
				// add a row into pivot table so can join to get quantity comment and stage etc
				$taskToMaterialToAssemblyToMaterial = new TaskToMaterialToAssemblyToMaterial();
				$taskToMaterialToAssemblyToMaterial->task_to_material_id = $taskToMaterial->id;
				$taskToMaterialToAssemblyToMaterial->assembly_to_material_id = $assemblyToMaterial->id;
				$taskToMaterialToAssemblyToMaterial->dbCallback('save');
			}
			$models[] = $taskToMaterial;
		}

		// recurse thru sub assemblies
		foreach(SubAssembly::model()->findAllByAttributes(array('parent_assembly_id'=>$assembly_id)) as $subAssembly)
		{
			// add quantity sub-assemblies
			for($cntr = 0; $cntr < $subAssembly->quantity; $cntr++)
			{
				$saved &= static::addAssembly($task_id, $subAssembly->child_assembly_id, $taskToAssembly->id, $models);
			}
		}
		
		return $saved;
	}

}

?>