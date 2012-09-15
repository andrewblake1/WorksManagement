<?php

class ReportController extends Controller
{
	private static $_model;
	private static $_errors;
	
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

	static function actionShow()
	{
		// get this report model
		self::$_model = Report::model()->findByPk($_GET['id']);

		// NB: most effecicient to ask the datbase directly if this report valid for this user
		// check if user belongs to a role that has access to this report
		$sql = 'SELECT COUNT(*) FROM `report_to_AuthItem` JOIN `AuthAssignment` WHERE `userid` = :userid';
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":userid", $userid = Yii::app()->user->id, PDO::PARAM_STR);
		if(0 == $command->queryScalar())
		{
			throw new CHttpException(403,'You do not have permission to view this report.');
		}
		
		// verify the context
		$controller = Yii::app()->controller;
		$_modelName = $controller->id->modelName;
		if(self::$_model->context && self::$_model->context != $_modelName)
		{
			throw new CHttpException(403,'The report isn\'t valid in this context.');
		}

		// get the primary key if any in play in this context
		if(isset($_SESSION[$_modelName]))
		{
			$pk = $_SESSION[$_modelName]['value'];
		}

		// do the substituitions
		$html = self::$_model->template_html;
		$html = preg_replace_callback('`\[(.*?)\]`', array(self, 'subReportCallback'), $html);
		
		// if errors
		if(!empty(self::$_errors))
		{
			$html = '<p>System admin SQL errors in report '.self::$_model->description.':</p>';
			$html += '<ul>';
			foreach($this::$_errors as &$error)
			{
				$html += "<li>$error</li>";
			}
			$html = '</ul>';
			throw new CHttpException(403, $html);
		}
		
		$controller->widget('ReportWidget',array(
			'report_html'=>$html,
		));
	}

	private static function subReportCallback($matches)
	{
		$subReportDescription = $matches[1];

		// get the sub report model
		$subReportModel = SubReport::model()->findByAttributes(array(
			'report_id'=>self::$_model->id,
			'description'=>$subReportDescription,
			));

		// the sql
		$sql = $subReportModel->select;
		
		// need to determine the count ourselves for when using CSqlDataProvider
		try
		{
			$count=Yii::app()->db->createCommand("SELECT COUNT(*) FROM ($sql) alias1")->queryScalar();
		}
		catch (CDbException $e)
		{
			$this::$_errors[] = $e->getMessage();
		}

		// if sql contains :userid
		if(stripos($sql, ':userid') !== false)
		{
			$params[':userid'] = Yii::app()->user->id;
		}

		// if sql contains :pk
		if(stripos($sql, ':pk') !== false)
		{
			// get the primary key if any in play in this context
			if(isset($_SESSION[$_modelName]))
			{
				$params[':pk'] = $_SESSION[$_modelName]['value'];
			}
			// otherwise error
			else
			{
				throw new CHttpException(403,'System admin error. The report isn\'t valid - primary key (:pk) in report but not in this context. ');
			}
		}

		// if not formatting data in grid - assuming scalar
		if($subReportModel->format == SubReport::subReportFormatNoFormat)
		{
			$html = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		// otherwise displaying in a grid
		else
		{
			// need to determine our own sort columns also with CSqlDataProvider
			$attributes=array_keys(Yii::app()->db->createCommand($sql)->queryRow());

			$options['totalItemCount'] = $count;
			$options['sort'] = array('attributes'=>$attributes);

			// if we need to page
			if($subReportModel->format == SubReport::subReportFormatPaged)
			{
				$options['pagination'] = array('pageSize'=>10);
			}
			
			if(!empty($params))
			{
				$options['params'] = $params;
			}
			
			// the data provider
			$dataProvider=new CSqlDataProvider($sql, $options);

			// get the grid
			ob_start();
			// display the grid
			Yii::app()->controller->widget('bootstrap.widgets.TbGridView',array(
				'id'=>'report-grid',
				'type'=>'striped',
				'dataProvider'=>$dataProvider,
//				'filter'=>$this->model,
				'columns'=>$attributes,
			));
			$html = ob_get_clean();
		}
		
		return $html;
	}

	protected function adminRender($_model)
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
			parent::adminRender($_model);
		}
		
	}

	protected function newButton()
	{
		echo ' ';
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'New',
			'url'=>$this->createUrl("{$this->modelName}/create"),
			'type'=>'primary',
			'size'=>'small', // '', 'large', 'small' or 'mini'
		));
	}

	protected function createRender($_model, $_models)
	{
		$_modelName = $this->modelName;

		// set heading
		if(!$this->heading)
		{	
			$this->heading .= $_modelName::getNiceName() . 's';
		}

		// set breadcrumbs
		$this->breadcrumbs = $this->getBreadCrumbTrail('Create');

		// set up tab menu if required - using setter
		$this->_tabs[0]['label'] = 'Create';
		$this->_tabs[0]['active'] = true;

		// NB: using update widget as clear of modal stuff intented on create in admin view
		$this->widget('UpdateViewWidget', array(
			'model'=>$_model,
			'models'=>$_models,
		));
	}

	protected function navbar()
	{
		// hide the navbar when showing a report
		if($this->action->Id == 'show')
		{
			return;
		}
		
		return parent::navbar();
	}

	// NB: drop down menu stops working when tinymce present hence hide the non working menu from system admin
	public function getReportsMenu()
	{
		// hide the reports menu when tinymce is present as drop down menu not working - javascript or css conflict
		if($this->action->Id == 'update')
		{
			return;
		}
		
		return parent::getReportsMenu();
	}

}

?>