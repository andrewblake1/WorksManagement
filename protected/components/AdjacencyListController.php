<?php
class AdjacencyListController extends Controller {

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

		if(!$parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null)
		{
			// if there is a primary key for this -- starting from beginning
			if(($update_id = static::getUpdateId(NULL, 0)) !== NULL)
			{
				// get the update models parent id
				$parent_id = $modelName::model()->findByPk($update_id)->parent_id;
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
	
}
?>
