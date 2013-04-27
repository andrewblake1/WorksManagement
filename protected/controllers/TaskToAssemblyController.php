<?php

class TaskToAssemblyController extends AdjacencyListController
{
	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = 'ViewTaskToAssembly';

	// called within AdminViewWidget
	public function getButtons($model)
	{
		return array(
			'class'=>'WMTbButtonColumn',
			'buttons'=>array(
				'delete' => array(
					'visible'=>'Yii::app()->user->checkAccess($data->id ? "TaskToAssembly" : "TaskToAssemblyToAssemblyToAssemblyGroup", array("primaryKey"=>$data->id ? $data->id : $data->assembly_group_id))',
					'url'=>'Yii::app()->createUrl(($data->id ? "TaskToAssembly" : "TaskToAssemblylToAssemblyGroupToAssembly" ).
							"/delete", array("id"=>$data->id ? $data->id : $data->assembly_group_id))',
				),
				'update' => array(
					'visible'=>'Yii::app()->user->checkAccess($data->assembly_group_id ? "TaskToAssemblyToAssemblyToAssemblyGroup" : "TaskToAssembly", array("primaryKey"=>$data->assembly_group_id ? $data->assembly_group_id : $data->id))',
					'url'=>'Yii::app()->createUrl(
						$data->assembly_group_id
							? $data->id
								? "TaskToAssemblyToAssemblyToAssemblyGroup/update"
								: "TaskToAssemblyToAssemblyToAssemblyGroup/create"
							: "TaskToAssembly/update",
						$data->assembly_group_id
							? array("id"=>$data->searchTaskToAssemblyToAssemblyToAssemblyGroupId, "TaskToAssemblyToAssemblyToAssemblyGroup"=>array(
								"assembly_group_to_assembly_id"=>$data->assembly_group_to_assembly_id,
								"assembly_group_id"=>$data->assembly_group_id,
								"task_id"=>$data->task_id,
								"task_to_assembly_id"=>($data->id
									? $data->id
									: $data->parent_id),
								"assembly_to_assembly_group_id"=>$data->assembly_to_assembly_group_id,
								))
							: array("id"=>$data->id)
					)',
				),
				'view' => array(
					'visible'=>'!Yii::app()->user->checkAccess($data->assembly_group_id ? "TaskToAssemblyToAssemblyToAssemblyGroup" : "TaskToAssembly", array("primaryKey"=>$data->assembly_group_id ? $data->assembly_group_id : $data->id))
						&& Yii::app()->user->checkAccess($data->assembly_group_id ? "TaskToAssemblyToAssemblyToAssemblyGroupRead" : "TaskToAssemblyRead")',
				'url'=>'Yii::app()->createUrl(($data->assembly_group_id ? "TaskToAssemblyToAssemblyToAssemblyGroup" : "TaskToAssembly" ).
					"/view", array("id"=>$data->assembly_group_id ? $data->assembly_group_id : $data->id))',
				),
			),
		);
	}

	public function getChildTabs($model, $last = FALSE)
	{
		$tabs = array();
		
		// add tab to  update TaskToAssembly
		$this->addTab(TaskToAssembly::getNiceName(NULL, $model),array(
			'TaskToAssembly/update',
			'id' => $model->id,
		), $tabs);

		// add tab to sub assemblies
		$this->addTab(SubAssembly::getNiceNamePlural(), array(
			'TaskToAssembly/admin',
			'parent_id' => $model->id,
			'task_id' => $model->task_id,
		), $tabs, !$last);

		// add tab to materials
		$this->addTab(Material::getNiceNamePlural(), array(
			'TaskToMaterial/admin',
			'task_to_assembly_id' => $model->id,
			'parent_id' => $model->id,	// needed for breadcrumb trail calc for adjacency list
			'task_id' => $model->task_id,
		), $tabs);

		// add tab to standard drawings
		$this->addTab(AssemblyToStandardDrawing::getNiceNamePlural(), array(
			'AssemblyToStandardDrawing/admin',
			'assembly_id' => $model->assembly_id,
			'parent_id' => $model->id,	// needed for breadcrumb trail calc for adjacency list
			'task_id' => $model->task_id,
			'task_to_assembly_id' => $model->id,
		), $tabs);

		return $tabs;
	}
	
	/**
	 * Recursive function to add assembly with sub assemblies to task. Inserts row into task_to_assembly and also appends materials to task_to_materials
	 * @param int $task_id the task id to add assembly to
	 * @param int $assembly_id the assembly id to add to task
	 * @param int $parent_id the id of the parent within this model - adjacency list
	 * @return returns 0, or null on error of any inserts
	 */
	static function addAssembly($task_id, $assembly_id, $quantity, $parent_id = null, &$models=array(), &$taskToAssembly=NULL)
	{
		// initialise the saved variable to show no errors
		$saved = true;
		
		// insert assembly into task_to_assembly table
		if($taskToAssembly === NULL)
		{
			$taskToAssembly = new TaskToAssembly();
		}
		$taskToAssembly->task_id = $task_id;
		$taskToAssembly->assembly_id = $assembly_id;
		$taskToAssembly->parent_id = $parent_id;
		$taskToAssembly->quantity = $quantity;
		// NB: can't call createSave due to recursion so this is internals
		$saved = $taskToAssembly->dbCallback('save');
		$models[] = $taskToAssembly;
		
		// insert materials into task_to_material table
		
		// AssemblyToMaterial
		foreach(AssemblyToMaterial::model()->findAllByAttributes(array('assembly_id'=>$assembly_id)) as $assemblyToMaterial)
		{
			$taskToMaterial = new TaskToMaterial();
			$taskToMaterial->task_id = $task_id;
			$taskToMaterial->material_id = $assemblyToMaterial->material_id;
			$taskToMaterial->task_to_assembly_id = $taskToAssembly->id;
			$taskToMaterial->store_id = $taskToAssembly->assembly->store_id;
			$taskToMaterial->quantity = $assemblyToMaterial->quantity;
			if($saved &= $taskToMaterial->createSave($models))
			{
				// add a row into pivot table so can join to get quantity comment and stage etc
				$taskToMaterialToAssemblyToMaterial = new TaskToMaterialToAssemblyToMaterial();
				$taskToMaterialToAssemblyToMaterial->task_to_material_id = $taskToMaterial->id;
				$taskToMaterialToAssemblyToMaterial->assembly_to_material_id = $assemblyToMaterial->id;
				$taskToMaterialToAssemblyToMaterial->createSave($models);
			}
		}

		// recurse thru sub assemblies
		foreach(SubAssembly::model()->findAllByAttributes(array('parent_assembly_id'=>$assembly_id)) as $subAssembly)
		{
			$saved &= static::addAssembly($task_id, $subAssembly->child_assembly_id, $subAssembly->quantity, $taskToAssembly->id, $models);
		}
		
		return $saved;
	}

	// override the tabs when viewing materials for a particular task - make match task_to_assembly view
	public function setTabs($model) {
		if($model)
		{
			parent::setTabs(NULL);
			$this->setChildTabs($this->loadModel(static::getUpdateId()));
		}
		else
		{
			parent::setTabs($model);
			// if in a sub assembly
			if($parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null)
			{
				$this->setChildTabs($this->loadModel($parent_id));
				// set breadcrumbs
				static::setUpdateId($parent_id);
				$this->breadcrumbs = self::getBreadCrumbTrail();
				array_pop($this->breadcrumbs);
				$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
				$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
				$this->breadcrumbs[] = SubAssembly::getNiceNamePlural();
			}
		}
	}

/*	public function setUpdateTabs($model) {
		// set top level tabs as per normal admin view
		parent::setTabs(false);
		
		$this->setChildTabs($this->loadModel(static::getUpdateId()));
	}*/
	
}

?>