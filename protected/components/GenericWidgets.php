<?php

/**
 * class Tree 
 */
class CategoryTree
{
	private $_modelName;
	private $_roots = array();
	
	public function __construct($modelName)
	{
		$this->_modelName = $modelName;
	}
	
	public function add($categoryId, $widgetHtml)
	{
		$modelName = $this->_modelName;

		// get array of parents which will be in reverse order
		for($parents = array(), $category = $modelName::model()->findByPk($categoryId); $category; $category = $category->getParent())
		{
			$parents[] = $category->name;
		}
	
		// ensure all these parents exist in our tree starting from the roots
		$treePointer = &$this->_roots;
		foreach($parents as $parent)
		{
			// if parent not already set
			if(!isset($treePointer[$parent]))
			{
				// add
				$treePointer[$parent] = array();
			}

			// shift
			$treePointer = &$treePointer[$parent];
		}
		
		// place our widget in the array at the end
		$treePointer[]=$widgetHtml;
	}
	
	// recursive
	public function display($treePointer = null)
	{
		if($treePointer === null)
		{
			$treePointer = $this->_roots;
		}

		foreach($treePointer as $key => &$value)
		{
			if(is_string($value))
			{
				echo $value;
			}
			else
			{
				echo '<fieldset class="fieldset">';
				echo "<legend>$key</legend>";
				$this->display($value);
				echo '</fieldset>';
			}	
		}
	}
}

class GenericWidgets extends CWidget
{
	private $controller;
	public $model;
	public $form;
	public $relation_modelToGenericModelType;
	public $relation_modelToGenericModelTypes;
	public $relation_genericModelType;
	public $relation_category;
	public $categoryModelName;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->controller = $this->getController();
	}
 
    public function run()
    {
		// create a new category tree
		$categoryTree = new CategoryTree($this->categoryModelName);
		
		// loop thru all the pivot table generic types associated to this model
//ere is where we need to use different array as no relation in row exists
		
//rojectToGenericProjectType::model()->findByAttributes()
		foreach($this->model->{$this->relation_modelToGenericModelTypes} as $toGenericType)
		{
			$genericModelType = $toGenericType->{$this->relation_genericModelType};
			$category = $genericModelType->{$this->relation_category};

			// get the widget html
			ob_start();
			$this->controller->widget('GenericWidget', array(
				'form'=>$this->form,
				'generic'=>$toGenericType->generic,
				'genericType'=>$toGenericType->{$this->relation_genericModelType}->genericType,
				'relationToGenericType'=>"{$this->relation_modelToGenericModelType}->{$this->relation_genericModelType}->genericType",
			));
			// add the widget html to the tree branch
			$categoryTree->add(isset($category->id) ? $category->id : null, ob_get_clean());
			
		}

		$categoryTree->display();
	}
	
}

?>