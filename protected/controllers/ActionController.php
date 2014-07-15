<?php

class ActionController extends Controller
{
	
	public static function setTrail ()
	{
		// adjust the trail to remove the invalid items so the correct breadcrumbs are generated as action can appear at 3 levels
		self::$trail = self::getTrail();
		
		if(isset($_GET['project_template_id']))
		{
			unset(self::$trail['Action']);
			unset(self::$trail['Client']['Action']);
		}
		elseif(isset($_GET['client_id']))
		{
			unset(self::$trail['Action']);
		}
		elseif(isset($_GET['action_id']))
		{
			$action = Action::model()->findByPk($_GET['action_id']);
			if($action->project_template_id)
			{
				unset(self::$trail['Action']);
				unset(self::$trail['Client']['Action']);
			}
			elseif($action->client_id)
			{
				unset(self::$trail['Action']);
			}
		}
				
		$trail=self::$trail;
		$getParams = Controller::getValidGetParams('DutyStep');
		
	}

	public function __construct($id, $module = null) {
		// hack prior to setTrail route is Action/update
		if(isset($_GET['id']))
		{
			// trick to get the below stuff to fire
			$_GET['action_id'] = $_GET['id'];
		}
		
		self::setTrail();
	
		parent::__construct($id, $module);
	}
	
	// called within AdminViewWidget
	// alter to maintain correct breadcrumb
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
	
	protected function createRedirect($model, $params = array())
	{
		parent::createRedirect($model, self::getCreateRedirectParams($this->modelName));
	}

	static public function getCreateRedirectParams($modelName) {
		$params = array();
		
		if(isset($_POST[$modelName]['client_id']))
		{
			$params['client_id'] = $_POST[$modelName]['client_id'];
		}
		
		if(isset($_POST[$modelName]['project_template_id']))
		{
			$params['project_template_id'] = $_POST[$modelName]['project_template_id'];
		}
		
		return $params;
	}

}

?>