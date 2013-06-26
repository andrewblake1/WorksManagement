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
	
	public function add($category_id, $widgetHtml)
	{
		$modelName = $this->_modelName;

		// get array of parents which will be in reverse order
		for($parents = array(), $category = $modelName::model()->findByPk($category_id); $category; $category = $category->getParent())
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

class CustomFieldWidgets extends CWidget
{
	private $controller;
	public $model;
	public $form;
	public $relationModelToCustomFieldModelTemplate;
	public $relationModelToCustomFieldModelTemplates;
	public $relationCustomFieldModelTemplate;
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
		
		echo CHtml::openTag('div', array('id'=>'customValues'));
		
		// loop thru all the pivot table custom fields
		foreach($this->model->{$this->relationModelToCustomFieldModelTemplates} as $toCustomField)
		{
			$CustomFieldModelTemplate = $toCustomField->{$this->relationCustomFieldModelTemplate};
			$category = $CustomFieldModelTemplate->{$this->relation_category};

			// get the widget html
			ob_start();
			$this->controller->widget('CustomFieldWidget', array(
				'form'=>$this->form,
				'customValue'=>$toCustomField,
				'customField'=>$toCustomField->{$this->relationCustomFieldModelTemplate}->customField,
				'relationToCustomField'=>"{$this->relationModelToCustomFieldModelTemplate}->{$this->relationCustomFieldModelTemplate}->customField",
			));
			// add the widget html to the tree branch
			$categoryTree->add(isset($category->id) ? $category->id : null, ob_get_clean());
			
		}

		$categoryTree->display();

		echo CHtml::closeTag('div', array('id'=>'CustomFields'));

	}
	
}

?>