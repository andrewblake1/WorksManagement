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
	public $modal_id;
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
		$this->beginWidget('bootstrap.widgets.TbModal', array(
			'id'=>$this->modal_id,
		));
		
		// set focus to first suitable field when modal opens
		Yii::app()->clientScript->registerScript('focus',
			"$('#myModal').on('shown', function () {
				$('#myModal input:not([class=\"hasDatepicker\"]):visible:enabled:first, #myModal textarea:first').first().focus();
			})",
			CClientScript::POS_READY);
		


		// if there is a view file then show as modal otherwise ignore
		try
		{
			$modelName = get_class($this->model);
			echo "<div id=\"form-create$modelName\" class=\"modal-body\">";
			echo $this->_controller->renderPartial('//'.lcfirst($this->_controller->modelName).'/_form',array(
				'model'=>$this->model,
				'models'=>$this->models,
				'parent_fk'=>$this->parent_fk,
			));
			echo '</div>';
		}
		catch(Exception $e)
		{
			// ignore errors rendering 
// TODO just make this ignore not found errors as in drawing to assembly view			

		}
 
		$this->endWidget(); 
	}
}

?>