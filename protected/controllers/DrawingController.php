<?php

class DrawingController extends TraitAdjacencyListWithFileController
{

	public function getChildTabs($model, $last = FALSE)
	{
		$modelName = $this->modelName;
		$tabs = array();
		
		parent::setTabs($model, $tabs);
		
		// dont add empty tabs if no write access
		if(static::checkAccess(self::accessWrite, $modelName) || DrawingAdjacencyList::model()->countByAttributes(array('parent_id' => $model->id)))
		{
			// add tab to drawings
			$this->addTab(
				Drawing::getNiceNamePlural(),
				'Drawing',
				'admin',
				array(
					'standard_id' => $model->standard_id,
					'parent_id' => $model->id
				),
				$tabs[0]
			);	
		}
	
		return $tabs[0];
	}
	
	// override the tabs when viewing assemblies for a particular task
	public function setTabs($model) {

		// control extra rows of tabs if action is 
		if($model)
		{
			if(isset($_GET['task_to_assembly_id']))
			{
				$taskToAssemblyController= new TaskToAssemblyController(NULL);
				$taskToAssembly = TaskToAssembly::model()->findByPk($_GET['task_to_assembly_id']);
				$taskToAssembly->assertFromParent();
				$taskToAssemblyController->setTabs(NULL);
				static::$tabs = $taskToAssemblyController->tabs;
				static::$tabs[sizeof(static::$tabs) - 1][3]['active'] = TRUE;

				$tabs=array();
				$this->addTab(
					Drawing::getNiceName($_GET['id']),
					Yii::app()->controller->modelName,
					Yii::app()->controller->action->id,
					$_GET,
					$tabs,
					TRUE
				);
				static::$tabs = array_merge(static::$tabs, array($tabs));

				// elimate irrelevant tabs
				$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail();
			}
			else
			{
				parent::setTabs(NULL);
				
				$this->setChildTabs($this->loadModel(static::getUpdateId()));
				$this->setActiveTabs(NULL, NULL, Drawing::getNiceNamePlural());
				$this->breadcrumbs = static::getBreadCrumbTrail();
			}
		}
		else
		{
			// be careful of any get params being appended - i.e. lower down sets parent id which don't want at
			// top level
			if(!empty($_GET['parent_id']))
			{
				$parent_id = $_GET['parent_id'];
				unset($_GET['parent_id']);
				parent::setTabs($model);
				$_GET['parent_id'] = $parent_id;
			}
			else {
				parent::setTabs($model);
			}

			if(isset($_GET['parent_id']))
			{
				$this->setChildTabs($this->loadModel($_GET['parent_id']));
				$this->setActiveTabs(NULL, Drawing::getNiceNamePlural(), Drawing::getNiceNamePlural());
			}

			$this->breadcrumbs = static::getBreadCrumbTrail();
		}
	}
	
	public function getRelation($model, $attribute)
	{
		if($attribute == 'parent_id')
		{
			return $model->parent;
		}
		
		return NULL;
	}


}

?>