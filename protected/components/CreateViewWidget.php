<?php

/**
 * Create view widget
 * @param ActiveRecord $model the model
 */
class CreateViewWidget extends CWidget
{
	private $controller;
	public $model;
	public $models;

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
		// add heading
		// add spaces after capitals
		$string=preg_replace(
			'/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/',
			' $1',
			$this->controller->modelName
		);
		
		$this->controller->breadcrumbs = $this->controller->getBreadCrumbTrail('Create');

		$this->controller->formTitle = "Create {$string}";
		
		echo $this->controller->render('_form',array(
			'model'=>$this->model,
			'models'=>$this->models,
			));
	}
}

?>
