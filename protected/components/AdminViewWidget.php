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
	}
 
    public function run()
    {
		// if system admin user
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
		}

		// add the buttons
		$this->columns[]=array('class'=>'WMTbButtonColumn');
	
		// add instructions
		$this->_controller->widget('bootstrap.widgets.TbAlert');

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
		$this->_controller->widget('CreateViewWidget', array(
			'model'=>$this->model,
		));

		
	}
}

?>
