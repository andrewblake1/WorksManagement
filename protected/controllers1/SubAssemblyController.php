<?php

class SubAssemblyController extends Controller
{
	
	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model) {

		// control extra rows of tabs if action is 
		if(isset($_GET['parent_assembly_id']))
		{
			// set tabs
			$assemblyController= new AssemblyController(NULL);
			$assembly = Assembly::model()->findByPk($_GET['parent_assembly_id']);
			$assembly->assertFromParent();
			$assemblyController->setTabs($assembly);
			$this->_tabs = $assemblyController->tabs;
			
			// set breadcrumbs
	//		static::setUpdateId(NULL, 'Assembly');
			$this->breadcrumbs = AssemblyController::getBreadCrumbTrail('Update');
			array_pop($this->breadcrumbs);
			$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
			$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
			$this->breadcrumbs[] = SubAssembly::getNiceName();
		}
		else
		{
			parent::setTabs($model);
		}
	}


}
