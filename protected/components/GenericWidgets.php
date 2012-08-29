<?php

/**
 * Generic widgets
 */
class GenericWidgets extends /*TbActiveForm*/CWidget
{
	private $controller;
	public $model;
	public $form;
	public $relation_modelToGenericModelType;
	public $relation_modelToGenericModelTypes;
	public $relation_genericModelType;

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
		// loop thru all the pivot table generic types associated to this model
		foreach($this->model->{$this->relation_modelToGenericModelTypes} as $toGenericType)
		{
			$this->controller->widget('GenericWidget', array(
				'form'=>$this->form,
				'generic'=>$toGenericType->generic,
				'genericType'=>$toGenericType->{$this->relation_genericModelType}->genericType,
				'relationToGenericType'=>"{$this->relation_modelToGenericModelType}->{$this->relation_genericModelType}->genericType",
			));
		}
	}
	
}

?>