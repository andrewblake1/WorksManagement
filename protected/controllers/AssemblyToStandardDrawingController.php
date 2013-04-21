<?php

class AssemblyToStandardDrawingController extends Controller
{
	
	// override the tabs when viewing materials for a particular task - make match task_to_assembly view
	public function setTabs($nextLevel = true) {

		// control extra rows of tabs if action is 
		if(isset($_GET['task_to_assembly_id']))
		{
			$taskToAssemblyController= new TaskToAssemblyController(NULL);
			$taskToAssembly = TaskToAssembly::model()->findByPk($_GET['task_to_assembly_id']);
			$taskToAssembly->assertFromParent();
			$taskToAssemblyController->setTabs(false);
			$this->_tabs = $taskToAssemblyController->tabs;
			
			// set breadcrumbs
			Controller::$nav['update']['TaskToAssembly'] = NULL;
			$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail('Update');
			array_pop($this->breadcrumbs);
			$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
			$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
			$this->breadcrumbs[] = AssemblyToStandardDrawing::getNiceName();
		}
		else
		{
			parent::setTabs($nextLevel);
		}
	}

}

?>