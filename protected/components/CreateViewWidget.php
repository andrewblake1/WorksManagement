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
	public $modalId;
	public $parent_fk;

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
		$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>$this->modalId));

		$modelName = get_class($this->model);
		echo "<div id=\"form-create$modelName\" class=\"modal-body\">";
		echo $this->_controller->renderPartial('//'.lcfirst($this->_controller->modelName).'/_form',array(
			'model'=>$this->model,
			'models'=>$this->models,
			'parent_fk'=>$this->parent_fk,
		));
		echo '</div>';
 
		$this->endWidget(); 
	}
}

?>