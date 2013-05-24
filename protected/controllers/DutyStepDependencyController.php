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
			static::$tabs = $dutyStepController->tabs;
		}
		else
		{
			parent::setTabs($model);
		}
		
		$this->breadcrumbs = DutyStepController::getBreadCrumbTrail('Update');
	}

}
