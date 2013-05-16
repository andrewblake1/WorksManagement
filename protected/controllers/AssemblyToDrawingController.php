<?php

class AssemblyToDrawingController extends Controller
{
	
	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model) {

		// control extra rows of tabs if action is 
		if(isset($_GET['task_to_assembly_id']))
		{
			// set tabs
			$taskToAssemblyController= new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($_GET['task_to_assembly_id']);
			$taskToAssembly->assertFromParent();
			$taskToAssemblyController->setTabs(NULL);
			$this->_tabs = $taskToAssemblyController->tabs;
			$this->setActiveTabs(NULL, Drawing::getNiceNamePlural(), SubAssembly::getNiceNamePlural());
			
			// set breadcrumbs
			static::setUpdateId(NULL, 'TaskToAssembly');
			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Update');
			array_pop($this->breadcrumbs);
			$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
			$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
			$this->breadcrumbs[] = AssemblyToDrawing::getNiceName();
		}
		elseif(isset($_GET['assembly_id']))
		{
			// set tabs
			$assemblyController= new AssemblyController(NULL);
			$assembly = Assembly::model()->findByPk($_GET['assembly_id']);
			$assembly->assertFromParent();
			$assemblyController->setTabs($assembly);
			$assemblyController->setActiveTabs(NULL, AssemblyToDrawing::getNiceNamePlural(), SubAssembly::getNiceNamePlural());
			$this->_tabs = $assemblyController->tabs;
			
			// set breadcrumbs
//			static::setUpdateId(NULL, 'Assembly');
			$this->breadcrumbs = AssemblyController::getBreadCrumbTrail('Update');
			array_pop($this->breadcrumbs);
			$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
			$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
			$this->breadcrumbs[] = AssemblyToDrawing::getNiceName();
		}
		else
		{
			parent::setTabs($model);
		}
	}

}

?>