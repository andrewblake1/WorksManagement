<?php
trait AdjacencyListControllerTrait {

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
				static::$tabs[] = $tabs;
			}
		}

		return static::$tabs;
	}

	
	static function getParentIds()
	{
		$controllerName = get_called_class();
		$modelName = $controllerName::modelName();

		if(!$parentId = isset($_GET['parent_id']) ? $_GET['parent_id'] : null)
		{
			// if there is a primary key for this -- starting from beginning
			if(($updateId = static::getUpdateId(NULL, 0)) !== NULL)
			{
				// get the update models parent id
				$parentId = $modelName::model()->findByPk($updateId)->parent_id;
			}
		}
		
		$ids = array();

		// if there is a parent
		for(; $parentId && $model = $modelName::model()->findByPk($parentId); $parentId = $model->parent_id)
		{
			$ids[] = $parentId;
		}

		return $ids;
	}
	
	static function currentTabLevel()
	{
		// get the target level
		$parentIds = static::getParentIds();

		return sizeof($parentIds);
	}
	
	protected function restoreAdminSettings(&$modelName, &$container = NULL)
	{
		if(isset($_SESSION['admin'][$modelName][static::currentTabLevel()]))
		{
			parent::restoreAdminSettings($modelName, $_SESSION['admin'][$modelName][static::currentTabLevel()]);
		}
	}	
	
	protected function storeAdminSettings(&$modelName, &$container = NULL)
	{
		$tabLevel = static::currentTabLevel();
		
		if(!isset($_SESSION['admin'][$modelName][$tabLevel]))
		{
			$_SESSION['admin'][$modelName][$tabLevel] = array();
		}

		parent::storeAdminSettings($modelName, $_SESSION['admin'][$modelName][$tabLevel]);
	}
	
}
?>
