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
//		if($this->_controller->checkAccess(Controller::accessRead))
//		{
//		// primary key name
//		$primaryKeyName = $this->model->tableSchema->primaryKey;
//		
		// show buttons on row by row basis i.e. do access check on context
		array_unshift($this->columns, array(
			'class'=>'WMTbButtonColumn',
			'buttons'=>array(
				'delete' => array(
					'visible'=>'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url'=>'Yii::app()->createUrl("'.$this->_controller->modelName.'/delete", array("'.$this->model->tableSchema->primaryKey.'"=>$data->primaryKey))',
				),
				'update' => array(
					'visible'=>'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url'=>'Yii::app()->createUrl("'.$this->_controller->modelName.'/update", array("'.$this->model->tableSchema->primaryKey.'"=>$data->primaryKey))',
				),
				'view' => array(
					'visible'=>'
						!Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))
						&& Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url'=>'Yii::app()->createUrl("'.$this->_controller->modelName.'/view", array("'.$this->model->tableSchema->primaryKey.'"=>$data->primaryKey))',
				),
			),
		));
//		}
	
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
//			'ajaxUrl'=>Yii::app()->request->requestUri,
		));

		// as using boostrap modal for create the html for the modal needs to be on
		// the calling page
		$this->_controller->actionCreate('myModal', $this->model);

		// add css overrides
		$sourceFolder = YiiBase::getPathOfAlias('webroot.css');
		$publishedFile = Yii::app()->assetManager->publish($sourceFolder . '/worksmanagement.css');
		Yii::app()->clientScript->registerCssFile($publishedFile);
		
		parent::run();
	}
}

?>