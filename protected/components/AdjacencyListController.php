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
		$queryParameters = array();
		if($parentForeignKey = $modelName::getParentForeignKey($parentCrumb = static::getParentCrumb($modelName)))
		{
			// starting from the beginning
			$paramsLevel0 = static::getAdminParams();
			if(isset($paramsLevel0[$parentForeignKey]))
			{
				$queryParameters[$parentForeignKey] = $paramsLevel0[$parentForeignKey];
			}
			elseif(static::getUpdateId($parentCrumb) !== NULL)
			{
				$queryParameters[$parentForeignKey] = static::getUpdateId($parentCrumb);
			}
		}

		$display = $modelName::getNiceName();
		$displays = $modelName::getNiceNamePlural();
		
		if(!$parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null)
		{
			// if there is a primary key for this -- starting from beginning
			if(($updateId = static::getUpdateId(NULL, 0)) !== NULL)
			{
				// get the update models parent id
				$parent_id = $modelName::model()->findByPk($updateId)->parent_id;
			}
		}	
		
		// if there is a parent
		for(; $model = $modelName::model()->findByPk($parent_id); $parent_id = $model->parent_id)
		{
			// get the text to display in the bread crumb
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
					$adjacencyListCrumbs[$text] = array("$modelName/admin") + $queryParameters + array('parent_id'=>$parent_id);
				}
				elseif($lastCrumb == 'Update')
				{
					// remove last item in existing breadcrumbs
					array_pop($breadcrumbs);
					// add a crumb with just the primary key nice name but no href
					$primaryKey = static::getUpdateId($modelName);
					$adjacencyListCrumbs[] = $modelName::getNiceName($primaryKey);
					// add crumb to admin view. NB using last query paramters to that admin view
					$adjacencyListCrumbs[$text] = array("$modelName/admin") + $queryParameters + array('parent_id'=>$parent_id);
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
				$adjacencyListCrumbs[$text] = array("$modelName/admin") + $queryParameters + array('parent_id'=>$parent_id);
			}
		}
		
		// if there is at least one parent
		if(count($adjacencyListCrumbs))
		{
			$breadcrumbs[$displays] = array("$modelName/admin") + $queryParameters;
			// append the breadcrumbs for the adjacency list
			$breadcrumbs += array_reverse($adjacencyListCrumbs);
		}

		return $breadcrumbs;
	}

	public function actionUpdate($id, $model = null) {
		$modelName = $this->modelName;
		$model = $this->loadModel($id, $model);
		
		static::setAdminParam('parent_id', $model->parent_id, $modelName);

		parent::actionUpdate($id, $model);
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
	
	static function getParentIds()
	{
		$controllerName = get_called_class();
		$modelName = $controllerName::modelName();

		if(!$parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null)
		{
			// if there is a primary key for this -- starting from beginning
			if(($updateId = static::getUpdateId(NULL, 0)) !== NULL)
			{
				// get the update models parent id
				$parent_id = $modelName::model()->findByPk($updateId)->parent_id;
			}
		}
		
		$ids = array();

		// if there is a parent
		for(; $model = $modelName::model()->findByPk($parent_id); $parent_id = $model->parent_id)
		{
			$ids[] = $parent_id;
		}
		
		return $ids;
	}
	
	static function currentTabLevel()
	{
		// get the target level
		$parentIds = static::getParentIds();

		return sizeof($parentIds);
	}
	
	protected function restoreAdminSettings(&$viewModelName, &$modelName, &$container = NULL)
	{
		parent::restoreAdminSettings($viewModelName, $modelName, $_SESSION['admin'][$modelName][static::currentTabLevel()]);
	}	
	
	protected function storeAdminSettings(&$viewModelName, &$modelName, &$container = NULL)
	{
		$tabLevel = static::currentTabLevel();
		
		if(!isset($_SESSION['admin'][$modelName][$tabLevel]))
		{
			$_SESSION['admin'][$modelName][$tabLevel] = array();
		}

		parent::storeAdminSettings($viewModelName, $modelName, $_SESSION['admin'][$modelName][$tabLevel]);
	}
	
/*	// return last or specified level of update id
	static function getUpdateId($modelName = NULL, $level = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		if($level === NULL)
		{
			$level = isset(Controller::$nav['update'][$modelName])
				? sizeof(Controller::$nav['update'][$modelName]) - 1
				: 0;
		}
$t = Controller::$nav;
		return isset(Controller::$nav['update'][$modelName][$level]) ? Controller::$nav['update'][$modelName][$level] : NULL;
	}
	
	// return last or specified level of update id
	static function setUpdateId($updateId, $modelName = NULL, $level = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		if($level === NULL)
		{
			$level = isset(Controller::$nav['update'][$modelName])
				? sizeof(Controller::$nav['update'][$modelName])
				: 0;
		}

		Controller::$nav['update'][$modelName][$level] = $updateId;
	}
	
	// return last or specified level of admin params
	static function getAdminParam($paramName, $modelName = NULL, int $level = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		if(isset(Controller::$nav['admin'][$modelName]))
		{
			$adminParam = &Controller::$nav['admin'][$modelName];

			if($level === NULL)
			{
				$level = sizeof($adminParam);
			}

			return isset($adminParams[$level - 1][$paramName]) ? $adminParams[$level - 1][$paramName] : NULL;
		}
	}

	// return last or specified level of admin params
	static function getAdminParams($modelName = NULL, int $level = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		if(isset(Controller::$nav['admin'][$modelName]))
		{
			$adminParams = &Controller::$nav['admin'][$modelName];

			if($level === NULL)
			{
				$level = sizeof($adminParams);
			}

			return isset($adminParams[$level - 1]) ? $adminParams[$level - 1] : array();
		}
		
		return array();
	}

	// level is array index i.e. starts at 0 and not 1
	static function setAdminParams($params, $modelName = NULL, int $level = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		if($level === NULL)
		{
			$level = isset(Controller::$nav['admin'][$modelName])
				? sizeof(Controller::$nav['admin'][$modelName])
				: 0;
		}
		
		Controller::$nav['admin'][$modelName][$level] = $params;
	}
*/
}
?>
