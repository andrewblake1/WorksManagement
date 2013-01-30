<?php

class PlanningController extends CategoryController
{
	protected $_adminView = 'categoryAdmin';

	public function accessRules()
	{
		$accessRules = parent::accessRules();
		array_unshift($accessRules,
			array('allow',
				'actions'=>array('addDay'),
				'roles'=>array($this->modelName),
		));

		return $accessRules;
	}

	public function actionFetchTree()
	{
		if(isset($_GET['project_id']))
		{
			$id = $_GET['project_id'];
		}
		else
		{
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}

		parent::actionFetchTree($id);
	}

	public function actionAddDay()
	{
		if(static::checkAccess(self::accessWrite, 'Day'))
		{
			// set post variables to simulate coming from a create click in the form
			$_POST[$this->modelName]['description'] = '';

			$day = new Day();
			$day->project_id = $_GET['project_id'];
			// create 
			DayController::createSaveStatic($day);
//			parent::actionCreate();
			$this->redirect(array('admin'.'?'.Yii::app()->request->queryString));
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
				'url'=>$this->createUrl("{$this->modelName}/addDay".'?'.Yii::app()->request->queryString),
				'type'=>'primary',
				'size'=>'small', // '', 'large', 'small' or 'mini'
			));
		}
	}
	
	public function actionCreate($modalId = 'myModal')
	{
		// can't create a planning, must create a project, or day or crew or task
		throw new CHttpException(403,'Invalid request.');
	}

	public function actionUpdate($id)
	{
		// can't update a planning, must update a project, or day or crew or task
		throw new CHttpException(403,'Invalid request.');
	}

	public function actionRename()
	{
		$id=$_POST['id'];
		$planning=$this->loadModel($id);
		if(static::checkAccess(self::accessWrite, Planning::$levels[$planning->level]))
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
		$planning=$this->loadModel($id);
		if(static::checkAccess(self::accessWrite, Planning::$levels[$planning->level]))
		{
			parent::actionRemove();
		}
		// otherwise doesn't have permission to be here
		else
		{
			throw new CHttpException(403,'You do not have permission.');
		}
	}
	
	// showing 1 level down from root here hence can never move roots so need to alter a post varialbe for the project id
	public function actionMoveCopy()
	{
		if($_POST['new_parent_root'] == 'root')
		{
			$moved_node_id=$_POST['moved_node'];
			$category=Planning::model()->findByPk($moved_node_id);
			$parent=$category->parent;
			$_POST['new_parent'] = $_POST['new_parent_root'] = $parent->id;
		}
		return parent::actionMoveCopy();
	}

}

?>