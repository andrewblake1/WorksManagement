<?php
class AdjacencyListActiveRecord extends ActiveRecord {

	/*
	 * attribute to be used in breadcrumb trail  - will use models display attribute otherwise
	 */
	public $crumbAttribute = 'description';

	// clear any levels below current tab level
	protected function clearForwardMemory(&$trail)
	{
		parent::clearForwardMemory($trail);
		
		$modelName = get_class($this);
		$controllerName = $modelName.'Controller';
		$level = $controllerName::currentTabLevel();
		
		if(isset($_SESSION['admin'][$modelName]))
		{
			foreach($_SESSION['admin'][$modelName] as $key => &$value)
			{
				if($key > $level)
				{
					unset($_SESSION['admin'][$modelName][$key]);
				}
			}
		}
	}

}

?>
