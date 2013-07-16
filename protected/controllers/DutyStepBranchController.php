<?php

class DutyStepBranchController extends Controller
{
	public function setTabs($model) {

		// in duty step dependency admin view
		$dutyStepDependencyController= new DutyStepDependencyController(NULL);
		$dutyStepDependency = DutyStepDependency::model()->findByPk(array_pop($dutyStepDependencyIds = $_GET['duty_step_dependency_ids']));
		$dutyStepDependency->assertFromParent();
		static::setUpdateId($dutyStepDependency->id, 'DutyStepDependency');
		$dutyStepDependencyController->setTabs($dutyStepDependency);
		$dutyStepDependencyController->setActiveTabs(NULL, DutyStepBranch::getNiceNamePlural(), DutyStepDependency::getNiceNamePlural());
		static::$tabs = $dutyStepDependencyController->tabs;
		
		$this->breadcrumbs = static::getBreadCrumbTrail();
	}
}
