<?php

class DutyController extends Controller
{
	
	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel = 'ViewDuty';

	public function setTabs($model, &$tabs = NULL) {
		// create tabs
		parent::setTabs($model, $tabs);
		// remove the first one
		unset(self::$tabs[0][0]);
		
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
		elseif(Yii::app()->user->checkAccess('Duty') || Yii::app()->user->checkAccess('DutyUpdate', array('assignedTo'=>$model->assignedTo)))
		{
			// can only update if not completed
			if(!empty($model->updated))
			{
				parent::actionUpdate($id);
			}
			// otherwise can view
			{
				$this->actionView($id);
			}
		}
		// otherwise doesn't have permission to be here
		else
		{
			throw new CHttpException(403,'You do not have permission to view this duty.');
		}
	}

}

?>