<?php

class ReportController extends Controller
{
	private $_dataProvider;
	
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
				'actions'=>array('show'),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array('admin','index','view', 'update'),
				'roles'=>array($this->modelName.'Read'),
			),
			array('allow',
				'actions'=>array('create','delete','update','autocomplete'),
				'roles'=>array($this->modelName),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionShow()
	{
		// get this report model
		$model = Report::model()->findByPk($_GET['id']);

		// NB: most effecicient to ask the datbase directly if this report valid for this user
		// check if user belongs to a role that has access to this report
		$sql = 'SELECT COUNT(*) FROM `report_to_AuthItem` JOIN `AuthAssignment` WHERE `userid` = :userid';
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":userid", $userid = Yii::app()->user->id, PDO::PARAM_STR);
		if(0 == $command->queryScalar())
		{
			throw new CHttpException(403,'You do not have permission to view this report.');
		}
		
		// need to determine the count ourselves for when using CSqlDataProvider
		$count=Yii::app()->db->createCommand("SELECT COUNT(*) FROM ({$model->select}) alias1")->queryScalar();
		
		// need to determine our own sort columns also with CSqlDataProvider
		$attributes=array_keys(Yii::app()->db->createCommand($model->select)->queryRow());
		
		// finally - the data provider
		$this->_dataProvider=new CSqlDataProvider($model->select, array(
			'totalItemCount'=>$count,
			'sort'=>array('attributes'=>$attributes),
			'pagination'=>array('pageSize'=>10)
		));

		Report::$niceName = $model->description;

		$this->actionAdmin();
	}

	protected function adminRender($model)
	{
		if(Yii::app()->controller->action->id == 'show')
		{
			// run the report i.e. an admin view
			$this->render('report',array(
				'dataProvider'=>$this->_dataProvider,
			));
		}
		else
		{
			parent::adminRender($model);
		}
		
	}

}

?>