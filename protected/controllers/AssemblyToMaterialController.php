<?php

class AssemblyToMaterialController extends Controller
{
	public function setTabs($model) {

		if(isset($_GET['assembly_id']))
		{
			// set tabs
			$assemblyController= new AssemblyController(NULL);
			$assembly = Assembly::model()->findByPk($_GET['assembly_id']);
			$assembly->assertFromParent();
			$assemblyController->setTabs($assembly);
			$assemblyController->setActiveTabs(NULL, AssemblyToMaterial::getNiceNamePlural(), SubAssembly::getNiceNamePlural());
			$this->_tabs = $assemblyController->tabs;
			
			// set breadcrumbs
//			static::setUpdate_id(NULL, 'Assembly');
			$this->breadcrumbs = AssemblyController::getBreadCrumbTrail('Update');
			array_pop($this->breadcrumbs);
			$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
			$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
			$this->breadcrumbs[] = AssemblyToMaterial::getNiceName();
		}
		else
		{
			parent::setTabs($model);
		}
	}
}

?>