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
									? array("id"=>$data->searchTaskToMaterialToAssemblyToMaterialGroupId, "TaskToMaterialToAssemblyToMaterialGroup"=>array(
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
	
	// override the tabs when viewing materials for a particular task - make match task_to_assembly view
	public function setTabs($nextLevel = true) {
		$modelName = $this->modelName;
		$update = FALSE;
			
		parent::setTabs($nextLevel);

		if(!empty($nextLevel->taskToAssembly->id))
		{
			$update = $parent_id = $nextLevel->taskToAssembly->id;
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
			$taskToAssembly->assertFromParent();
			$taskToAssemblyController->setTabs(false);
			$taskToAssemblyController->setActiveTabs(NULL, $modelName::getNiceNamePlural());
			$this->_tabs = $taskToAssemblyController->tabs;

			Controller::$nav['update']['TaskToAssembly'] = NULL;
			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Update');

			$lastLabel = $modelName::getNiceName(isset($_GET['id']) ? $_GET['id'] : NULL);

			if($update)
			{
				$tabs=array();
				$this->addTab($lastLabel, Yii::app()->request->requestUri, $tabs, TRUE);
				$this->_tabs = array_merge($this->_tabs, array($tabs));
				array_pop($this->breadcrumbs);
				$this->breadcrumbs[$modelName::getNiceNamePlural()] = Yii::app()->request->requestUri;
				$this->breadcrumbs[] = $lastLabel;
			}
			else
			{
				array_pop($this->breadcrumbs);
				$this->breadcrumbs[] = $modelName::getNiceNamePlural();
			}

		}
		
/*		
		
		// control extra rows of tabs if action is 
		if($this->action->id == 'admin')
		{
			if(isset($_GET['task_to_assembly_id']))
			{
				$taskToAssemblyController= new TaskToAssemblyController(NULL);
				$taskToAssembly = TaskToAssembly::model()->findByPk($_GET['task_to_assembly_id']);
				$this->_tabs =  array_merge($this->_tabs, $taskToAssemblyController->setChildTabs($taskToAssembly));
				if(sizeof($this->_tabs > 1))
				{
					// adjust active tab from material to sub assembly
					// beware this obviously tab order dependant
					$this->_tabs[0][3]['active'] = TRUE;
					$this->_tabs[0][2]['active'] = FALSE;
				}
			}
			
			// set breadcrumbs
			Controller::$nav['update']['TaskToAssembly'] = NULL;
			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Update');
			array_pop($this->breadcrumbs);
			$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
			$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
			$this->breadcrumbs[] = TaskToAssembly::getNiceName();
		}*/
	}

}

?>