<?php

class AssemblyToAssemblyGroupController extends Controller
{
	public function setTabs($model) {

		if(isset($_GET['sub_assembly_ids']))
		{
			// in sub assembly admin view
			$subAssemblyController= new SubAssemblyController(NULL);
			$subAssembly = SubAssembly::model()->findByPk(array_pop($subAssemblyIds = $_GET['sub_assembly_ids']));
			$subAssembly->assertFromParent();
			static::setUpdateId($subAssembly->id, 'SubAssembly');
			$subAssemblyController->setTabs($subAssembly);
			$subAssemblyController->setActiveTabs(NULL, AssemblyToAssemblyGroup::getNiceNamePlural(), SubAssembly::getNiceNamePlural());
			static::$tabs = $subAssemblyController->tabs;
		}
		elseif(isset($_GET['assembly_id']))
		{
			// set tabs
			$assemblyController= new AssemblyController(NULL);
			$assembly = Assembly::model()->findByPk($_GET['assembly_id']);
			$assembly->assertFromParent();
			$assemblyController->setTabs($assembly);
			$assemblyController->setActiveTabs(NULL, AssemblyToAssemblyGroup::getNiceNamePlural(), SubAssembly::getNiceNamePlural());
			static::$tabs = $assemblyController->tabs;
		}
		else
		{
			parent::setTabs($model);
		}
		
		$this->breadcrumbs = AssemblyController::getBreadCrumbTrail();
	}
}
