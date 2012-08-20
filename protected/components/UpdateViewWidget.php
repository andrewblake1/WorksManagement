<?php

/**
 * Update view widget
 * @param ActiveRecord $model the model
 */
class UpdateViewWidget extends CWidget
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
		echo $this->controller->render('_form',array(
			'model'=>$this->model,
			'models'=>$this->models,
			));
	}
}

?>