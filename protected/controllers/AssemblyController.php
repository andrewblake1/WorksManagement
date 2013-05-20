<?php

class AssemblyController extends AdjacencyListController
{
	 
	public function getChildTabs($model, $last = FALSE)
	{
		$tabs = array();
		
		parent::setTabs($model, $tabs);
		
		return $tabs[0];
	}

	// override the tabs when viewing assemblies for a particular task
	public function setTabs($model) {
		if($model)
		{
			parent::setTabs(NULL);
			$this->setChildTabs($this->loadModel(static::getUpdateId()));
		}
		else
		{
			parent::setTabs($model);
			// if in a sub assembly
			if($parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null)
			{
				$this->setChildTabs($this->loadModel($parent_id));
				// set breadcrumbs
				static::setUpdateId($parent_id);
				$this->breadcrumbs = self::getBreadCrumbTrail();
				array_pop($this->breadcrumbs);
				$updateTab = $this->_tabs[sizeof($this->_tabs) - 1][0];
				$this->breadcrumbs[$updateTab['label']] = $updateTab['url'];
				$this->breadcrumbs[] = SubAssembly::getNiceNamePlural();
			}
		}
	}

}

?>