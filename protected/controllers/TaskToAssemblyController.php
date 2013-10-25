<?php

class TaskToAssemblyController extends Controller
{
	use AdjacencyListControllerTrait;

	// called within AdminViewWidget
	public function getButtons($model)
	{
//TODO: repeated
		// add jquery responsible for handling multiple form possiblities here - i.e. this handling multiple models
		// need to re-read modal contents first as selecting a different record
		$modelName = $this->modelName;
		Yii::app()->clientScript->registerScript('onclickReturnForm', "
			function onclickReturnForm(obj)
			{
				// need to re-read modal contents first as selecting a different record
				$.ajax({
					type: 'POST',
					url: $(obj).attr('href'),
					'beforeSend' : function(){
						$('#$modelName-grid').addClass('ajax-sending');
					},
					'complete' : function(){
						$('#$modelName-grid').removeClass('ajax-sending');
					},
					success: function(data){
						// change the contents
						$('#myModal div').html(data);
						// display the modal
						$('#myModal').modal('show');

					} //success
				});//ajax
			}
		", CClientScript::POS_END);
		
		return array(
			'class'=>'WMTbButtonColumn',
			'buttons'=>array(
				'delete' => array(
					'visible'=>'Yii::app()->user->checkAccess("' . $this->modelName . '")',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/delete", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
				'update' => array(
					'visible'=>'Yii::app()->user->checkAccess("' . $this->modelName . '")',
					'url'=>'Yii::app()->createUrl(
						$data->assembly_group_id
							? ($data->assembly_to_assembly_group_id
								? "TaskToAssemblyToAssemblyToAssemblyGroup/returnForm"
								: "TaskToAssemblyToTaskTemplateToAssemblyGroup/returnForm")
							: "TaskToAssembly/update",

						$data->assembly_group_id
							? ($data->assembly_to_assembly_group_id
								? (array("id"=>$data->task_to_assembly_to_assembly_to_assembly_group_id, "TaskToAssemblyToAssemblyToAssemblyGroup"=>array(
									"assembly_group_id"					=>$data->assembly_group_id,
									"task_id"							=>$data->task_id,
									"task_to_assembly_id"				=>$data->id,
									"parent_id"							=>$data->parent_id,
									"assembly_to_assembly_group_id"		=>$data->assembly_to_assembly_group_id,
									)))
								: (array("id"=>$data->task_to_assembly_to_task_template_to_assembly_group_id, "TaskToAssemblyToTaskTemplateToAssemblyGroup"=>array(
									"assembly_group_id"					=>$data->assembly_group_id,
									"task_id"							=>$data->task_id,
									"task_to_assembly_id"				=>$data->id,
									"task_template_to_assembly_group_id"		=>$data->task_template_to_assembly_group_id,
									))))
							: array("id"=>$data->id)
					)',
					'click'=>'function() {if($(this).attr("href").indexOf("returnForm") >= 0) { onclickReturnForm(this); return false; }}',
				),
				'view' => array(
					'visible'=>'
						!Yii::app()->user->checkAccess("' . $this->modelName . '")
						&& Yii::app()->user->checkAccess("' . $this->modelName . 'Read")',
					'url'=>'Yii::app()->createUrl((
						$data->assembly_group_id
							? ($data->assembly_to_assembly_group_id
								? "TaskToAssemblyToAssemblyToAssemblyGroup"
								: "TaskToAssemblyToTaskTemplateToAssemblyGroup")
							: "TaskToAssembly"
					) . "/view", array("id"=>
						$data->assembly_group_id
							? $data->assembly_group_id
							: $data->id
					))',
				),
			),
		);
	}

	/**
	 * Recursive factory method to add assembly with sub assemblies to task. Inserts row into taskToAssembly and also appends materials to taskToMaterials
	 * @param int $task_id the task id to add assembly to
	 * @param int $assembly_id the assembly id to add to task
	 * @param int $parent_id the id of the parent within this model - adjacency list
	 * @return returns 0, or null on error of any inserts
	 */
	static function addAssembly($task_id, $assembly_id, $quantity, $parent_id = NULL, $sub_assembly_id = NULL, &$models=array(), &$taskToAssembly=NULL)
	{
		// initialise the saved variable to show no errors
		$saved = true;
		
		// insert assembly into taskToAssembly table
		if($taskToAssembly === NULL)
		{
			$taskToAssembly = new TaskToAssembly();
		}
		$taskToAssembly->task_id = $task_id;
		$taskToAssembly->assembly_id = $assembly_id;
		$taskToAssembly->parent_id = $parent_id;
		$taskToAssembly->sub_assembly_id = $sub_assembly_id;
		$taskToAssembly->quantity = $quantity;
		// NB: can't call createSave due to recursion so this is internals
		$saved = $taskToAssembly->dbCallback('save');
		$models[] = $taskToAssembly;
		
		// insert materials into taskToMaterial table
		
		// AssemblyToMaterial
		foreach(AssemblyToMaterial::model()->findAllByAttributes(array('assembly_id'=>$assembly_id)) as $assemblyToMaterial)
		{
			$taskToMaterial = new TaskToMaterial();
			$taskToMaterial->task_id = $task_id;
			$taskToMaterial->material_id = $assemblyToMaterial->material_id;
			$taskToMaterial->task_to_assembly_id = $taskToAssembly->id;
			$taskToMaterial->standard_id = $taskToAssembly->assembly->standard_id;
			$taskToMaterial->quantity = $taskToMaterial->getDefault($assemblyToMaterial);
			if($saved &= $taskToMaterial->createSave($models))
			{
				// add a row into pivot table so can join to get quantity comment and stage etc
				$taskToMaterialToAssemblyToMaterial = new TaskToMaterialToAssemblyToMaterial();
				$taskToMaterialToAssemblyToMaterial->task_to_material_id = $taskToMaterial->id;
				$taskToMaterialToAssemblyToMaterial->assembly_to_material_id = $assemblyToMaterial->id;
				$taskToMaterialToAssemblyToMaterial->createSave($models);
			}
		}

		// AssemblyToMaterialGroup
		foreach(AssemblyToMaterialGroup::model()->findAllByAttributes(array('assembly_id'=>$assembly_id)) as $assemblyToMaterialGroup)
		{
			$taskToMaterialToAssemblyToMaterialGroup = new TaskToMaterialToAssemblyToMaterialGroup;
			$taskToMaterialToAssemblyToMaterialGroup->quantity = 0; // Dummy to pass validation designed for form update
			$taskToMaterialToAssemblyToMaterialGroup->task_id = $task_id;
//			$taskToMaterialToAssemblyToMaterialGroup->task_to_assembly_id = $taskToAssembly->id;
			$taskToMaterialToAssemblyToMaterialGroup->material_group_id = $assemblyToMaterialGroup->material_group_id;
			$taskToMaterialToAssemblyToMaterialGroup->assembly_to_material_group_id = $assemblyToMaterialGroup->id;
			$taskToMaterialToAssemblyToMaterialGroup->createSave($models);
		}

		// AssemblyToAssemblyGroup
		foreach(AssemblyToAssemblyGroup::model()->findAllByAttributes(array('assembly_id'=>$assembly_id)) as $assemblyToAssemblyGroup)
		{
			$taskToAssemblyToAssemblyToAssemblyGroup = new TaskToAssemblyToAssemblyToAssemblyGroup;
			$taskToAssemblyToAssemblyToAssemblyGroup->quantity = 0; // Dummy to pass validation designed for form update
			$taskToAssemblyToAssemblyToAssemblyGroup->task_id = $task_id;
//			$taskToAssemblyToAssemblyToAssemblyGroup->task_to_assembly_id = $taskToAssembly->id;
			$taskToAssemblyToAssemblyToAssemblyGroup->assembly_group_id = $assemblyToAssemblyGroup->assembly_group_id;
			$taskToAssemblyToAssemblyToAssemblyGroup->assembly_to_assembly_group_id = $assemblyToAssemblyGroup->id;
			$taskToAssemblyToAssemblyToAssemblyGroup->createSave($models);
		}

		// recurse thru sub assemblies
		foreach(SubAssembly::model()->findAllByAttributes(array('parent_assembly_id'=>$assembly_id)) as $subAssembly)
		{
			$saved &= static::addAssembly($task_id, $subAssembly->child_assembly_id, TaskToAssembly::getDefault($subAssembly), $taskToAssembly->id, $subAssembly->id, $models);
		}
		
		return $saved;
	}

	public function getChildTabs($model, $last = FALSE)
	{
		$tabs = array();
		
		// add tab to  update TaskToAssembly
		$this->addTab(
			TaskToAssembly::getNiceName(NULL, $model),
			'TaskToAssembly',
			'update',
			array('id' => $model->id),
			$tabs
		);

		// add tab to sub assemblies
		$this->addTab(
			SubAssembly::getNiceNamePlural(),
			'TaskToAssembly',
			'admin',
			array('parent_id' => $model->id, 'task_id' => $model->task_id),
			$tabs,
			!$last
		);

		// add tab to materials
		$this->addTab(
			Material::getNiceNamePlural(),
			'TaskToMaterial',
			'admin',
			array(
				'task_to_assembly_id' => $model->id,
				'parent_id' => $model->id,	// needed for breadcrumb trail calc for adjacency list
				'task_id' => $model->task_id
			),
			$tabs
		);

		return $tabs;
	}
	
	// override the tabs when viewing assemblies for a particular task
	public function setTabs($model) {

		if(!empty($_GET['parent_id']))
		{
			$parent_id = $_GET['parent_id'];
			unset($_GET['parent_id']);
		}
		
		// top level - becarefule not to carry across parent_id as a get param for this or will effect future search
		parent::setTabs($model ? NULL : $model);
		
		// restore get
		if(!empty($parent_id))
		{
			$_GET['parent_id'] = $parent_id;
		}

		if($model)
		{
			$this->setChildTabs($this->loadModel(static::getUpdateId()));
		}
		// if in a sub assembly
		elseif(isset($parent_id))
		{
			$this->setChildTabs($this->loadModel($parent_id));
		}

		$this->setActiveTabs(NULL,
			$model
				? TaskToAssembly::getNiceName(NULL, $model)
				: (empty($parent_id)
					? TaskToAssembly::getNiceNamePlural()
					: SubAssembly::getNiceNamePlural())
		);		
		
		$this->breadcrumbs = self::getBreadCrumbTrail();
	}
	
}

?>