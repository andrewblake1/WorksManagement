<?php

class ReportController extends Controller
{
	private static $_model;
	private static $_errors;
	
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
		$context = $_GET['context'];

		// determine if this user has access to this report
		foreach(self::$_model->reportToAuthItems as $reportToAuthItem)
		{
			if(Yii::app()->user->checkAccess($reportToAuthItem->auth_item_name))
			{
				$hasAccess = true;
			}
		}

		if(empty($hasAccess))
		{
			throw new CHttpException(403,'You do not have permission to view this report.');
		}
		
		// verify the context
		$controller = Yii::app()->controller;
		if(self::$_model->context && self::$_model->context != $context)
		{
			throw new CHttpException(403,'The report isn\'t valid in this context.');
		}

		$html = self::$_model->template_html;
		
		// get the primary key if any in play in this context
		$pk = null;
		// if pk passed
		if(!empty($_GET['pk']))
		{
			$pk = $_GET['pk'];
			// adding parameter to callback function - to avoid a global
			$callback = function( $matches ) use ( $pk ) {
				return ReportController::subReportCallback($matches, $pk);
			};

			$html = preg_replace_callback('`\{(.*?)\}`', $callback, $html);
		}

		
		// if errors
		if(!empty(self::$_errors))
		{
			$html = '<p>System admin SQL errors in report '.self::$_model->description.':</p>';
			$html .= '<ul>';
			foreach(self::$_errors as &$error)
			{
				$html .= "<li>$error</li>";
			}
			$html .= '</ul>';
			throw new CHttpException(403, $html);
		}
		
		$controller->widget('ReportWidget',array(
			'report_html'=>$html,
		));
	}

	static function subReportCallback($matches, $pk)
	{
		$html = '';
		$subReportDescription = $matches[1];

		// get the sub report model
		if(!$subReportModel = SubReport::model()->findByAttributes(array(
			'report_id'=>self::$_model->id,
			'description'=>$subReportDescription,
		)))
		{
			throw new CHttpException(403, "System admin error. Sub report '$subReportDescription' does not exist.");
		}

		// the sql
		$sql = $subReportModel->select;

		// create commands
		$countCommand=Yii::app()->db->createCommand("SELECT COUNT(*) FROM ($sql) alias1");
		$command=Yii::app()->db->createCommand($sql);
		
		// if sql contains :userid
		if(stripos($sql, ':userid') !== false)
		{
			$params[':userid'] = Yii::app()->user->id;
			$countCommand->bindParam(":userid", $params[':userid'], PDO::PARAM_INT);
			$command->bindParam(":userid", $params[':userid'], PDO::PARAM_INT);
		}

		// if sql contains :pk
		if(stripos($sql, ':pk') !== false)
		{
			// get the primary key if any in play in this context
			if($pk !== null)
			{
				$params[':pk'] = $pk;
				$countCommand->bindParam(":pk", $params[':pk'], PDO::PARAM_STR);
				$command->bindParam(":pk", $params[':pk'], PDO::PARAM_STR);
			}
			// otherwise error
			else
			{
				throw new CHttpException(403,'System admin error. The report isn\'t valid - primary key (:pk) in report but not in this context.');
			}
		}

		// need to determine the count ourselves for when using CSqlDataProvider
		try
		{
			$count = $countCommand->queryScalar();
		}
		catch (CDbException $e)
		{
			static::$_errors[] = $e->getMessage();
		}

		// if not formatting data in grid - assuming scalar
		if($subReportModel->format == SubReport::subReportFormatNoFormat)
		{
			$html = $command->queryScalar();
		}
		// otherwise displaying in a grid
		elseif($count)
		{
//$t=array_keys($command->queryRow());
			// need to determine our own sort columns also with CSqlDataProvider
			$attributes=array();
			// get any link columns which have :link appended to column name
			foreach(array_keys($command->queryRow()) as $attribute)
			{
				$exploded = explode(':', $attribute);
				if(sizeof($exploded) > 1)
				{
					$name = $exploded[0];
					$type = $exploded[1];
					// set attribute
					$attributes[$name] = array(
						'name'=>$name,
						'type'=>'raw',
					);
					// set sort
					$options['sort']['attributes'][] = $name;
					// fix the sql
					$sql = str_replace($attribute, $name, $sql);
				}
				else
				{
					$name = $attributes[$attribute] = $attribute;
					$options['sort']['attributes'][] = $attribute;
				}
				
				if(!isset($options['keyField']))
				{
					// set key field as first field - needs a key field otherwise default to id and if not there errors
					$options['keyField'] = $name;
				}
			}

			$options['totalItemCount'] = $count;

			// if we need to page
			if($subReportModel->format == SubReport::subReportFormatPaged)
			{
				// set the cgridview template to include paged stuff
				$template = '{items}\n{pager}';
				$options['pagination'] = array('pageSize'=>10);
				// Create filter model and set properties
				$filtersForm=new FiltersForm;
				if(isset($_GET['FiltersForm']))
				{
					$filtersForm->filters = $_GET['FiltersForm'];
					foreach($attributes as &$attribute)
					{
						if(!empty($_GET['FiltersForm'][$attribute]))
						{
							$where[$attribute] = " `$attribute` LIKE :$attribute ";
							$options['params'][":$attribute"] = "%{$_GET['FiltersForm'][$attribute]}%";
						}
					}
					$where = implode(' AND ', $where);
					$sql = "SELECT * FROM ($sql) t WHERE $where";
				}
			}
			else
			{
				// NB: pagination needs to be set to false in order to stop paginating
				$options['pagination'] =  false;
				// set the cgridview template to exclude paged stuff
				$template = '{items}';
			}

			if(!empty($params))
			{
				$options['params'] = $params;
			}
						
			// the data provider
			$dataProvider=new CSqlDataProvider($sql, $options);

			// get the grid
			ob_start();

			// if we need to page
			if($subReportModel->format == SubReport::subReportFormatPaged)
			{
				// if exporting to xl
				if(isset($_GET['action']) && $_GET['action'] == 'download')
				{
					// Export it
					Yii::app()->controller->toExcel($dataProvider, $attributes, null, array(), 'CSV'/*'Excel5'*/);
				}
		// TODO excel5 has issue on isys server likely caused by part of phpexcel wanting access to /tmp but denied		
		// TODO excel2007 best format however mixed results getting succesfull creations with this = varies across servers likely php_zip issue	thnk
		// it works on windows machine however not mac nor linux for me so far.

				// export button
				echo '<h2>';
					Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
						'label'=>'Download Excel',
						'url'=>Yii::app()->controller->createUrl("show", $_GET + array('action'=>'download')),
						'type'=>'primary',
						'size'=>'small', // '', 'large', 'small' or 'mini'
					));
				echo '</h2>';
			}

			// display the grid
			Yii::app()->controller->widget('bootstrap.widgets.TbGridView',array(
				'id'=>'report-grid',
				'type'=>'striped',
				'dataProvider'=>$dataProvider,
//				'filter'=>$filtersForm,
				'columns'=>$attributes,
				'template'=>"{items}\n{pager}",

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

	protected function createRender($model, $models, $modal_id)
	{
		// don't do this in admin view - this is special case where we don't render as modal in admin view
		if($this->action->id == 'create')
		{
			$modelName = $this->modelName;

			// set heading
			if(!$this->heading)
			{	
				$this->heading .= $modelName::getNiceName() . 's';
			}

			// set breadcrumbs
			$this->breadcrumbs = $this->getBreadCrumbTrail('Create');

			// set up tab menu if required - using setter
			$this->_tabs[0][0]['label'] = 'Create';
			$this->_tabs[0][0]['active'] = true;

			echo $this->render('_form',array(
				'model'=>$model,
				'models'=>$models,
				));
		}
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
			return null;
		}
		
		return parent::getReportsMenu();
	}

}

?>