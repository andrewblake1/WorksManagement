<?php
class AdjacencyListController extends Controller {

	/**
	 * Add additional crumbs showing the trail within
	 */
	static function getBreadCrumbTrail($lastCrumb = NULL)
	{
		$breadcrumbs = parent::getBreadCrumbTrail($lastCrumb);
		$adjacencyListCrumbs = array();
		$modelName = static::modelName();

		// the only query parameter we want to allow is the foreign key to the parent
		$queryParamters = array();
		if($parentForeignKey = $modelName::getParentForeignKey($parentCrumb = static::getParentCrumb($modelName)))
		{
			if(isset(Controller::$nav['admin'][$modelName][$parentForeignKey]))
			{
				$queryParamters[$parentForeignKey] = Controller::$nav['admin'][$modelName][$parentForeignKey];
			}
			elseif(isset(Controller::$nav['update'][$parentCrumb]))
			{
				$queryParamters[$parentForeignKey] = Controller::$nav['update'][$parentCrumb];
			}
		}

		$display = $modelName::getNiceName();
		$displays = $modelName::getNiceNamePlural();
		
		if(!$parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null)
		{
			// if there is a primary key for this
			if(isset(Controller::$nav['update'][$modelName]))
			{
				// get the update models parent id
				$parent_id = $modelName::model()->findByPk(Controller::$nav['update'][$modelName])->parent_id;
			}
		}	
		
		// if there is a parent
		for(; $model = $modelName::model()->findByPk($parent_id); $parent_id = $model->parent_id)
		{
			// get the text to display in the bread rum
			$text = isset($model->{$model->crumbAttribute}) ? $model->{$model->crumbAttribute} : $modelName::getNiceName($parent_id);

			// if this is the last crumb
			if(!count($adjacencyListCrumbs))
			{
				// remove last item in existing breadcrumbs
				array_pop($breadcrumbs);
				if($lastCrumb == 'Create')
				{
					// remove last item in existing breadcrumbs
					array_pop($breadcrumbs);
					// add last crumb
					$adjacencyListCrumbs[] = $lastCrumb;
					// add crumb to admin view
					$adjacencyListCrumbs[$text] = array("$modelName/admin") + $queryParamters + array('parent_id'=>$parent_id);
				}
				elseif($lastCrumb == 'Update')
				{
					// remove last item in existing breadcrumbs
					array_pop($breadcrumbs);
					// add a crumb with just the primary key nice name but no href
					$primaryKey = Controller::$nav['update'][$modelName];
					$adjacencyListCrumbs[] = $modelName::getNiceName($primaryKey);
					// add crumb to admin view. NB using last query paramters to that admin view
					$adjacencyListCrumbs[$text] = array("$modelName/admin") + $queryParamters + array('parent_id'=>$parent_id);
				}
				else
				{
					$adjacencyListCrumbs[] = $text;
				}
			}
			// otherwise not last crumb
			else
			{
				// add crumb to admin view
				$adjacencyListCrumbs[$text] = array("$modelName/admin") + $queryParamters + array('parent_id'=>$parent_id);
			}
		}
		
		// if there is at least one parent
		if(count($adjacencyListCrumbs))
		{
			$breadcrumbs[$displays] = array("$modelName/admin") + $queryParamters;
			// append the breadcrumbs for the adjacency list
			$breadcrumbs += array_reverse($adjacencyListCrumbs);
		}

		return $breadcrumbs;
	}

	public function actionUpdate($id, $model = null) {
		$modelName = $this->modelName;
		$model = $this->loadModel($id, $model);
		
		Controller::$nav['admin'][$modelName]['parent_id'] = $model->parent_id;

		parent::actionUpdate($id, $model);
	}
	
	public function setUpdateTabs($model) {
		// set top level tabs as per normal
		parent::setTabs(false);
		
		$this->setChildTabs($this->loadModel(Controller::$nav['update'][$this->modelName]));
	}

	public function setChildTabs($model)
	{
		$models = array();

		for($models[] = $model; $model = $model->parent; $models[] = $model);
		$size = sizeof($models);
		$cntr = 0;
		foreach(array_reverse($models) as $model)
		{
			$cntr++;
			if($tabs = $this->getChildTabs($model, $cntr == $size))
			{
				$this->_tabs[] = $tabs;
			}
		}

		return $this->_tabs;
	}

	public function getChildTabs($model, $last = FALSE)
	{

	}
	
	public function setTabs($nextLevel = true) {
		parent::setTabs($nextLevel);
		// control extra rows of tabs if action is 
//		if($this->action->id == 'admin')
//		{
			if(isset($_GET['parent_id']))
			{
				$this->setChildTabs($this->loadModel($_GET['parent_id']));
			}
//		}
	}

}
?>
