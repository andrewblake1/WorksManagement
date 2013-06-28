<?php

class DutyController extends Controller
{
	
	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = 'ViewDuty';
	
	public function getButtons($model)
	{
		$controllerName = str_replace('Controller', '', get_called_class());
		
		return array(
			'class' => 'WMTbButtonColumn',
			'buttons' => array(
				'delete' => array(
					'visible' => 'FALSE',
					
				),
				'update' => array(
					'visible' => 'Duty::model()->checkAccess(Controller::accessWrite, $data)',
					'url' => 'Yii::app()->createUrl("'. $controllerName. '/update", array("id"=>$data->primaryKey))',
				),
				'view' => array(
					'visible' => '
						!Duty::model()->checkAccess(Controller::accessWrite, $data)
						&& Duty::model()->checkAccess(Controller::accessRead, $data)',
					'url' => 'Yii::app()->createUrl("'. $controllerName. '/view", array("id"=>$data->primaryKey))',
				),
			),
		);
	}

	public function setTabs($model, &$tabs = NULL) {
		// create tabs
		parent::setTabs($model, $tabs);
		
		if(!$model)
		{
			// remove the first one
			unset(self::$tabs[0][0]);
		}
		
		// alter the breadcrumb trail also to stop an error with the virtual TaskToAdmin parent
		// basically alter the url of the virtual TaskToAdmin item to have no url
		
		// get the array index to the duties tab
		$niceNameDuty = Duty::getNiceNamePlural('Duty');
		foreach($this->breadcrumbs as $key => &$crumb)
		{
			if(key($crumb) == $niceNameDuty)
			{
				$dutyKey = $key;
				break;
			}
		}
		$this->breadcrumbs[$dutyKey - 1] = array(key($this->breadcrumbs[$dutyKey - 1]));
	}

	protected static function makeCrumbAdmin($displays, $queryParamters)
	{
		$modelName = static::modelName();
		
		// if a duty given
		if($dutyId = static::getUpdateId())
		{
			$viewDuty = ViewDuty::model()->findByPk($dutyId);
			return array($displays => array("$modelName/admin") + array(
				'task_id'=>$viewDuty->task_id,
				'action_id'=>$viewDuty->action_id,
			));
		}
		else
		{
			return parent::makeCrumbAdmin($displays, $queryParamters);
		}
	}

	/**
	 * Specifies the access control rules.
	 * NB: need to override this to open up so can shift access control into actionUpdate method to pass params for bizrule
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('admin','index','view', 'update'),
				'roles'=>array($this->modelName.'Read'),
			),
			array('allow',
				'actions'=>array('create','delete','autocomplete'),
				'roles'=>array($this->modelName),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	// special handling of update for duties
	public function actionUpdate($id)
	{
		// get duty
		$model = Duty::model()->findByPk($id);

		// system admin
		if(Yii::app()->user->checkAccess('system admin'))
		{
			// is the only one allowed to alter once ticked off as complete
			parent::actionUpdate($id);
		}
		// other users with full Duty access or has DutyUpdate permission - has to be assigned to this duty
		elseif($model->checkAccess(Controller::accessWrite) && empty($model->updated))
		{
			parent::actionUpdate($id);
		}
		elseif($model->checkAccess(Controller::accessRead))
		{
			$this->actionView($id);
		}
		// otherwise doesn't have permission to be here
		else
		{
			throw new CHttpException(403,'You do not have permission to view this duty.');
		}
	}
	
	// redirect to admin
	protected function adminRedirect($model, $sortByNewest = false) {

		// if posted a controller then this is where we should return to
		if (!empty($_POST['controller']))
		{
			$modelName = $_POST['controller'];
		}
		else
		{
			$modelName = get_class($model);
		}

		// because we are redirecting back to duty whos parent is the virtual model TaskToAction, we actually need
		// to pass action_id and task_id instead of task_to_action_id
	
		$params = array_merge(array("$modelName/admin"),  (static::getAdminParams($modelName) + array(
			'action_id'=>$model->action_id,
			'task_id'=>$model->task_id,
		)));

		// if we want to sort by the newest record first
		if ($sortByNewest) {
			$model->adminReset();
			$params["{$modelName}_sort"] = $modelName::model()->tableSchema->primaryKey . '.desc';
		}

		$this->redirect($params);
	}
	
	// need to show previous steps custom fields on duty form as disabled - recursive
	private function previousStepsCustomFields($model, $form)
	{
		// loop thru previous duty steps
		foreach(ViewDuty::model()->findAll($immediateDependencies = $model->immediateDependencies) as $duty)
		{
			$this->widget('CustomFieldWidgets',array(
				'model'=>$duty,
				'form'=>$form,
				'relationModelToCustomFieldModelTemplate'=>'dutyDataToCustomFieldToDutyStep',
				'relationModelToCustomFieldModelTemplates'=>'dutyData->dutyDataToCustomFieldToDutySteps',
				'relationCustomFieldModelTemplate'=>'customFieldToDutyStep',
				'relation_category'=>'customFieldDutyStepCategory',
				'categoryModelName'=>'CustomFieldDutyStepCategory',
				'htmlOptions'=>array('disabled'=>'disabled'),
			));
			
			// recurse thru any children
			foreach(ViewDuty::model()->findAll($immediateDependencies = $duty->immediateDependencies) as $duty)
			{
				$this->previousStepsCustomFields($duty);
			}
		}
		
	}

}

?>