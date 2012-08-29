<?php

/**
 * Admin view widget
 * @param ActiveRecord $model the model
 * @param array $columns the table columns to display in the grid view
 */
class AdminViewWidget extends CWidget
{
	private $_controller;
	public $model;
	public $columns;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->_controller = $this->getController();

		parent::init();
	}
 
    public function run()
    {
/*		// if system admin user and audit scenario
		if(Yii::app()->user->checkAccess('system admin'))
		{
			// add the staff column
			$this->columns[]=array(
					'name'=>'searchStaff',
					'value'=>'CHtml::link($data->searchStaff,
						Yii::app()->createUrl("Staff/update", array("id"=>$data->staff_id))
					)',
					'type'=>'raw',
				);
		}*/

		// add the buttons - first determine if there are any!
		if($this->_controller->checkAccess(Controller::accessWrite))
		{
			$this->columns[]=array('class'=>'WMTbButtonColumn');
		}
	
		// add instructions/ warnings errors via Yii::app()->user->setFlash
		// NB: thia won't work on ajax update as in delete hence afterDelete javascript added in WMTbButtonColumn
		$this->_controller->widget('bootstrap.widgets.TbAlert');

// TODO: figure out how to use this for the error message flash in WMTbButtonColumn - also slow the fade futher
/*		// add fade out to the flash message
		Yii::app()->clientScript->registerScript(
			'myHideEffect',
			'$(".alert-info").animate({opacity: 1.0}, 10000).fadeOut("slow");',
			CClientScript::POS_READY
		);*/
		
		// display the grid
		$this->_controller->widget('bootstrap.widgets.TbGridView',array(
			'id'=>$this->_controller->modelName.'-grid',
			'type'=>'striped',
			'dataProvider'=>$this->model->search(),
			'filter'=>$this->model,
			'columns'=>$this->columns,
		));

		// as using boostrap modal for create the html for the modal needs to be on
		// the calling page
		$this->_controller->actionCreate();

		parent::run();
	}
}

?>