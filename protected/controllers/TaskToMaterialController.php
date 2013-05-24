<?php

class TaskToMaterialController extends Controller
{
	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = 'ViewTaskToMaterial';

	// called within AdminViewWidget
	public function getButtons($model)
	{
		return array(
			'class'=>'WMTbButtonColumn',
			'buttons'=>array(
				'delete' => array(
					'visible'=>'Yii::app()->user->checkAccess($data->id ? "TaskToMaterial" : "TaskToMaterialToAssemblyToMaterialGroup", array("primaryKey"=>$data->id ? $data->id : $data->material_group_id))',
					'url'=>'Yii::app()->createUrl(($data->id ? "TaskToMaterial" : "TaskToMaterialToAssemblyToMaterialGroup" ).
							"/delete", array("id"=>$data->id ? $data->id : $data->material_group_id))',
				),
				'update' => array(
					'visible'=>'Yii::app()->user->checkAccess($data->material_group_id ? "TaskToMaterialToAssemblyToMaterialGroup" : "TaskToMaterial", array("primaryKey"=>$data->material_group_id ? $data->material_group_id : $data->id))',
					'url'=>'Yii::app()->createUrl(
								$data->material_group_id
									? $data->id
										? "TaskToMaterialToAssemblyToMaterialGroup/update"
										: "TaskToMaterialToAssemblyToMaterialGroup/create"
									: "TaskToMaterial/update",
								$data->material_group_id
									? array("id"=>$data->search_task_to_material_to_assembly_to_material_group_id, "TaskToMaterialToAssemblyToMaterialGroup"=>array(
										"material_group_to_material_id"=>$data->material_group_to_material_id,
										"material_group_id"=>$data->material_group_id,
										"material_id"=>$data->material_id,
										"task_id"=>$data->task_id,
										"task_to_assembly_id"=>$data->task_to_assembly_id,
										"assembly_to_material_group_id"=>$data->assembly_to_material_group_id,
										))
									: array("id"=>$data->id)
							)',
				),
				'view' => array(
					'visible'=>'!Yii::app()->user->checkAccess($data->material_group_id ? "TaskToMaterialToAssemblyToMaterialGroup" : "TaskToMaterial", array("primaryKey"=>$data->material_group_id ? $data->material_group_id : $data->id))
						&& Yii::app()->user->checkAccess($data->material_group_id ? "TaskToMaterialToAssemblyToMaterialGroupRead" : "TaskToMaterialRead")',
				'url'=>'Yii::app()->createUrl(($data->material_group_id ? "TaskToMaterialToAssemblyToMaterialGroup" : "TaskToMaterial" ).
							"/view", array("id"=>$data->material_group_id ? $data->material_group_id : $data->id))',
					),
			),
		);
	}
	
	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model) {
		$modelName = $this->modelName;
		$update = FALSE;
			
		parent::setTabs($model);

		if(!empty($model->taskToAssembly->id))
		{
			$update = $parent_id = $model->taskToAssembly->id;
		}
		elseif(isset($_GET['task_to_assembly_id']))
		{
			$parent_id = $_GET['task_to_assembly_id'];
		}
		if(!empty($parent_id))
		{
			$_GET['parent_id'] = $task_to_assembly_id = $parent_id;
			$taskToAssemblyController= new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($task_to_assembly_id);
//			$taskToAssembly->assertFromParent();
			$taskToAssemblyController->setTabs(NULL);
			$taskToAssemblyController->setActiveTabs(TaskToAssembly::getNiceNamePlural(), $modelName::getNiceNamePlural());
			static::$tabs = $taskToAssemblyController->tabs;

			static::setUpdateId(NULL, 'TaskToAssembly');
//			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Update');


			if($update)
			{
				$lastLabel = $modelName::getNiceName(isset($_GET['id']) ? $_GET['id'] : NULL);
				$tabs=array();
				$this->addTab($lastLabel, Yii::app()->request->requestUri, $tabs, TRUE);
				static::$tabs = array_merge(static::$tabs, array($tabs));
			}

			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Update');
		}
		
	}

}

?>