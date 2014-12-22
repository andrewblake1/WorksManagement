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
		$pk = empty($_GET['pk'])
			? empty($_GET['id'])
				? NULL
				: $_GET['id']
			: $_GET['pk'];

		// if pk passed
		if($pk)
		{
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

	public static function createSubReportCommand($sql, &$pk, &$userId, &$params)
	{
		// create the command
		$command = Yii::app()->db->createCommand($sql);

		// if sql contains :userid
		if(stripos($sql, ':userid') !== false)
		{
			$params[':userid'] = $userId;
			$command->bindParam(":userid", $params[':userid'], PDO::PARAM_INT);
		}

		// if sql contains :pk
		if(stripos($sql, ':pk') !== false)
		{
			// get the primary key if any in play in this context
			if($pk !== null)
			{
				$params[':pk'] = $pk;
				$command->bindParam(":pk", $params[':pk'], PDO::PARAM_STR);
			}
			// otherwise error
			else
			{
				throw new CHttpException(403,'System admin error. The report isn\'t valid - primary key (:pk) in report but not in this context.');
			}
		}
		
		return $command;
	}

	static function subReportCallback($matches, $pk)
	{
		$html = '';
		$subReportDescription = $matches[1];
		$userId = Yii::app()->user->id;
		$params = array();
		$export = isset($_GET['action']) && $_GET['action'] == 'download';

		// get the sub report model
		if(!$subReportModel = SubReport::model()->findByAttributes(array(
			'report_id'=>self::$_model->id,
			'description'=>$subReportDescription,
		)))
		{
			throw new CHttpException(403, "System admin error. Sub report '$subReportDescription' does not exist.");
		}

		// make username available as variable for selects
		$command = Yii::app()->db->createCommand('SET @username = :username');
		$user = User::model()->findByPk(Yii::app()->user->id);
		$username =$user->contact->first_name .' ' . $user->contact->last_name;
		$command->bindParam(":username", $username, PDO::PARAM_STR);
		$command->execute();
		
		// make contactid available as variable for selects
		$command = Yii::app()->db->createCommand('SET @contact_id = :contact_id');
		$user = User::model()->findByPk(Yii::app()->user->id);
		$contact_id =$user->contact->id;
		$command->bindParam(":contact_id", $contact_id, PDO::PARAM_STR);
		$command->execute();

		// this could before a multi-statement that might do something like createing a temporary table so in this case the sql we
		// want to deal with is the last one, and all previous ones are just to be executed
		// The last one is our sql
		// The last one is` our sql - array filter removes blank elements created eg. by ; at end
		$sqls = array_filter(explode(';', $subReportModel->select));
		$sql = array_pop($sqls);

		// execute any others
		foreach($sqls as $excuteSql)
		{
			static::createSubReportCommand($excuteSql, $pk, $userId, $params)->execute();
		}

		// create commands
		$countCommand=static::createSubReportCommand("SELECT COUNT(*) FROM ($sql) alias1", $pk, $userId, $params);
		$command=static::createSubReportCommand($sql, $pk, $userId, $params);
		
		// need to determine the count ourselves for when using CSqlDataProvider
		try
		{
			$count = $countCommand->queryScalar();
		}
		catch (CDbException $e)
		{
			static::$_errors[] = $e->getMessage();
			return;
		}

		// if not formatting data in grid - assuming scalar
		if($subReportModel->format == SubReport::subReportFormatNoFormat)
		{
			$html = $command->queryScalar();
		}
		// otherwise displaying in a grid
		elseif($count)
		{
			// need to determine our own sort columns also with CSqlDataProvider
			$attributes=array();
			// get any link columns which have :link appended to column name
			foreach(array_keys($command->queryRow()) as $attribute)
			{
//				$attribute = str_replace(' ', '', $attribute);
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
					$name = $attribute;
					$attributes[$attribute] = array(
						'name'=>$attribute,
						'type'=>'raw',
					);
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
				$options['pagination'] = $export ? false : array('pageSize'=>10);
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
			if($subReportModel->format == SubReport::subReportFormatPaged || ($subReportModel->format == SubReport::subReportFormatNotPaged))
			{
				// if exporting to xl
				if($export && $_GET['subreport'] == $subReportModel->id)
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
						'url'=>Yii::app()->controller->createUrl("show", $_GET + array('action'=>'download', 'subreport'=>$subReportModel->id)),
						'type'=>'primary',
						'size'=>'small', // '', 'large', 'small' or 'mini'
					));
				echo '</h2>';
			}

			// display the grid
			static $cntr = 0;
			Yii::app()->controller->widget('WMTbExtendedGridView',array(
				'id'=>'report-grid' . $cntr++,
				'type'=>'striped',
				'dataProvider'=>$dataProvider,
//				'filter'=>$filtersForm,
				'columns'=>$attributes,
				'template'=>$template,
				'heading'=>$subReportModel->description,
			));
			$html = ob_get_clean();
		}
		
		return $html;
	}
	
	public function exportButton ()
	{
		// already dealing with export button so don't need one at the bottom
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

			// set up tab menu if required - using setter
			static::$tabs[0][0]['label'] = 'Create';
			static::$tabs[0][0]['active'] = true;
			// set breadcrumbs
			$this->breadcrumbs = $this->getBreadCrumbTrail();

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
	public function getReportsMenu($reportType = 0, $context = NULL)
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
