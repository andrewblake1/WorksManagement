<?php

class ActionToPlantController extends Controller
{
	public function __construct($id, $module = null) {
		
		ActionController::setTrail();
	
		parent::__construct($id, $module);
	}
	
	protected function createRedirect($model, $params = array())
	{
		parent::createRedirect($model, ActionController::getCreateRedirectParams($this->modelName));
	}
	
	public function getButtons($model)
	{
		return array(
			'class' => 'WMTbButtonColumn',
			'buttons' => array(
				'delete' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("' . get_class($model) . '/delete", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
				'update' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("' . get_class($model) . '/update", array_merge(array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey), $_GET))',
				),
				'view' => array(
					'visible' => '
						!Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))
						&& Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => 'Yii::app()->createUrl("' . get_class($model) . '/view", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
			),
		);
	}	

}
