<?php

class ScheduleController extends CategoryController
{
	/**
	 * @var string the name of the admin view
	 */
	protected $_adminView = 'categoryAdmin';

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return parent::accessRules() + array(
			array('allow',
				'actions'=>array('addDay'),
				'roles'=>array($this->modelName),
			),
		);
	}

	public function actionFetchTree()
	{
		parent::actionFetchTree($_SESSION['actionAdminGet']['Schedule']['project_id']);
	}

	public function actionAddDay()
	{
		if(static::checkAccess(self::accessWrite, 'Day'))
		{
			// set post variables to simulate coming from a create click in the form
			$_POST[$this->modelName]['description'] = '';

			$day = new Day();
			$day->project_id = $_SESSION['Project']['value'];
			DayController::createSaveStatic($day);
			parent::actionCreate();
		}
		// otherwise doesn't have permission to be here
		else
		{
			throw new CHttpException(403,'You do not have permission.');
		}
	}

	protected function newButton()
	{
		if(static::checkAccess(self::accessWrite, 'Day'))
		{
			echo ' ';
			$this->widget('bootstrap.widgets.TbButton', array(
				'label'=>'New day',
				'url'=>$this->createUrl("{$this->modelName}/addDay"),
				'type'=>'primary',
				'size'=>'small', // '', 'large', 'small' or 'mini'
			));
		}
	}

	
	public function actionCreate($modalId = 'myModal')
	{
		// can't create a schedule, must create a project, or day or crew or task
		throw new CHttpException(403,'Invalid request.');
	}

	public function actionUpdate($id)
	{
		// can't update a schedule, must update a project, or day or crew or task
		throw new CHttpException(403,'Invalid request.');
	}

	public function actionRename()
	{
		$id=$_POST['id'];
		$schedule=$this->loadModel($id);
		if(static::checkAccess(self::accessWrite, Schedule::$levels[$schedule->level]))
		{
			parent::actionRename();
		}
		// otherwise doesn't have permission to be here
		else
		{
			throw new CHttpException(403,'You do not have permission.');
		}
	}

	public function actionRemove()
	{
		$id=$_POST['id'];
		$schedule=$this->loadModel($id);
		if(static::checkAccess(self::accessWrite, Schedule::$levels[$schedule->level]))
		{
			parent::actionRemove();
		}
		// otherwise doesn't have permission to be here
		else
		{
			throw new CHttpException(403,'You do not have permission.');
		}
	}
	
}

?>