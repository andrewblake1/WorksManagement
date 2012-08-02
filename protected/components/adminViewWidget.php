<?php

/**
 * Admin view widget
 * @param ActiveRecord $model the model
 * @param array $columns the table columns to display in the grid view
 */
class adminViewWidget extends CWidget
{
	private $controller;

	public $model;
	public $columns;

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
		$this->columns[]=array('class'=>'WMBootButtonColumn');

		// add heading
		// add spaces after capitals
		$string=preg_replace(
			'/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/',
			' $1',
			$this->controller->modelName
		);
		echo "<h1>Manage {$string}s</h1>";

		// add instructions
		$this->widget('bootstrap.widgets.BootAlert');

		// display the grid
		$this->controller->widget('bootstrap.widgets.BootGridView',array(
			'id'=>$this->controller->modelName.'-grid',
			'type'=>'striped',
			'dataProvider'=>$this->model->search(),
			'filter'=>$this->model,
			'columns'=>$this->columns,
		));

	}
}

?>
