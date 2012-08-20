<?php

/**
 * Generic widgets
 */
class ResourceWidgets extends TbActiveForm
{
	private $controller;
	public $model;
	public $form;

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
		$this->controller->widget('AdminViewWidget',array(
			'model'=>new TaskToResourceType('search'),
			'heading'=>'Resources',
			'columns'=>array(
				'id',
				array(
					'name'=>'searchResourceType',
					'value'=>'CHtml::link($data->searchResourceType,
						Yii::app()->createUrl("ResourceType/update", array("id"=>$data->resource_type_id))
					)',
					'type'=>'raw',
				),
				'quantity',
				'hours',
				'start',
			),
		));
	}
}

?>