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
//		// ajax submit set in WMTbActiveForm as url paramter for the ajax submit validate url
//		if(!isset($_GET['ajaxsubmit']))
//		{
			$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>$this->modalId));
//		}

		echo '<div id="form-create" class="modal-body">';
		echo $this->_controller->renderPartial('//'.$this->_controller->modelName.'/_form',array(
			'model'=>$this->model,
			'models'=>$this->models,
			'parent_fk'=>$parent_fk,
		));
		echo '</div>';
 
//		if(!isset($_GET['ajaxsubmit']))
//		{
			$this->endWidget(); 
//		}
	}
}

?>