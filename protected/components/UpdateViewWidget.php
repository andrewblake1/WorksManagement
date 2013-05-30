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
	public $parent_fk;

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
		// set focus to first suitable field when modal opens
		Yii::app()->clientScript->registerScript('focus',
			"$('form input:not([class=\"hasDatepicker\"]):visible:enabled:first, textarea:first').first().focus();",

			CClientScript::POS_READY);
		echo $this->controller->render('_form',array(
			'model'=>$this->model,
			'models'=>$this->models,
			'parent_fk'=>$this->parent_fk,
		));
	}
}

?>