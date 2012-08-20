<?php

/**
 * Create view widget
 * @param ActiveRecord $model the model
 * @param ActiveRecord $models the models for a multi model view
 */
class CreateViewWidget extends CWidget
{
	private $_controller;
	public $model;
	public $models;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->_controller = $this->getController();
	}

    public function run()
    {
		$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal'));

		echo '<div class="modal-body">';
		echo $this->_controller->renderPartial('_form',array(
			'model'=>$this->model,
			'models'=>$this->models,
		));
		echo '</div>';
 
		$this->endWidget(); 
	}
}

?>