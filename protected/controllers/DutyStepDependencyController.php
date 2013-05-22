<?php

class DutyStepDependencyController extends Controller
{
	
	// override the tabs when viewing materials for a particular task - make match taskToAssembly view
	public function setTabs($model) {

		// control extra rows of tabs if action is 
		if(isset($_GET['parent_duty_step_id']))
		{
			// set tabs
			$dutyStepController= new DutyStepController(NULL);
			$dutyStep = DutyStep::model()->findByPk($_GET['parent_duty_step_id']);
			$dutyStep->assertFromParent();
			$dutyStepController->setTabs($dutyStep);
			$this->_tabs = $dutyStepController->tabs;
			
			// set breadcrumbs
			$this->breadcrumbs = DutyStepController::getBreadCrumbTrail('Update');
			array_pop($this->breadcrumbs);
			$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
			$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
			$this->breadcrumbs[] = DutyStepDependency::getNiceName();
		}
		else
		{
			parent::setTabs($model);
		}
	}


}
