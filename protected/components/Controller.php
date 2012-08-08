<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	/**
	 * @var array the buttons to go in Manage cgridview
	 */
	public $buttons;
	/**
	 * @var string the model name
	 */
	public $modelName;
	/**
	 * @var string the flash message to show sort and search instructions
	 */
	const messageSortSearch = '<p><strong>To sort,</strong> click on column name.
		<p><strong>To search,</strong> enter part of any term and click elsewhere.
		If a column name has () then you can search the different attributes by separating with /.';
	/**
	 * @var string the flash message to show sort and search adn compare instructions
	 */
	const messageSortSearchCompare = "<p><strong>To sort,</strong> click on column name.
		<p><strong>To search,</strong> enter part of any term and click elsewhere.
		If a column name has () then you can search the different attributes by separating with /.
		<p>You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>,
		<b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values.";
	/**
	 * @var array provides the hierachy for a breadcrumb trail
	 */
	private $trail = array(
		'Client'=>array(
			'TaskType'=>array(
				'TaskType'=>array(
					'TaskTypeToDutyType',
				),
			),
		),
		'Project'=>array(
			'ProjectToAuthAssignment'=>array(
				'ProjectToAuthAssignmentToTaskTypeToDutyType',
			),
			'ProjectToGenericProjectType'
		),
		'Task'=>array(
			'Crew',
			'Day',
			'Duty',
			'Reschedule',
			'PurchaseOrders',
			'MaterialToTask',
			'TaskToAssembly',
			'TaskToGenericTaskType',
			'TaskToResourceType',
		),
		'Material',
		'Assembly'=>array(
			'Plan',
		),
		'Staff'=>array(
			'AuthAssignment',
		),
		'AuthItem',
		'GenericProjectType',
		'Genericprojectcategory',
		'GenericTaskType'=>array(
			'Generictaskcategory',
		),
		'DutyType'=>array(
			'Dutycategory',
		),
		'GenericType',
		'ResourceType'=>array(
			'Resourcecategory',
		),
	);
	
	public function __construct($id, $module = null)
	{
		$this->modelName = str_replace('Controller', '', get_class($this));
		
		$this->buttons = array(
				'class'=>'CButtonColumn',
				'template'=>'{update}{delete}',
				'buttons'=>array(
					'update'=>array(
						'imageUrl'=>Yii::app()->getAssetManager()->publish(
							Yii::getPathOfAlias('zii.widgets.assets')).'/gridview/view.png',
					),
				),
			);
		
		parent::__construct($id, $module);
	}

	// data provider for EJuiAutoCompleteFkField
	public function actionFKAutocomplete()
	{
		// if something has been entered
		if (isset($_GET['term']))
		{
			// url parameters
			$fKModelType = $_GET['fk_model'];
			$model = $fKModelType::model();
			// protect against possible injection
			$criteria = new CDbCriteria;
			$criteria->params = array();
			$terms = explode(Yii::app()->params['delimiter']['search'], $_GET['term']);

			// key will contain either a number or a foreign key field in which case field will be the lookup value
			foreach($fKModelType::getDisplayAttr() as $key => $field)
			{
				// building display parameter which gets eval'd later
				$display .= (isset($display) ? ".Yii::app()->params['delimiter']['display']." : '') . '$p->';

				// building display parameter which gets eval'd later
				// get term for this column from users entry
				// with trailing wildcard only; probably a good idea for large volumes of data
				$term = ($term = each($terms)) ? trim($term['value']) . '%' : '%';

				// if we are using a foreign key lookup
				if(!is_numeric($key))
				{
					$criteria->with[] = $key;
					$display .= str_replace('.', '->', $key).'->';
					$criteria->order[] = "$key.$field asc";
					$paramName = ':'.str_replace('.', '', $key).$field;
					$criteria->condition .= ($criteria->condition ? " AND " : '')."$key.$field like $paramName";
					$criteria->params["$paramName"] = $term;
				}
				else
				{
					$criteria->order[] = "$field asc";
					$criteria->condition .= ($criteria->condition ? " AND " : '')."$field like :$field";
					$criteria->params[":$field"] = $term;
				}
				$display .= $field;
			}

			// probably a good idea to limit the results
			$criteria->limit = 20;
			$criteria->order = implode(', ', $criteria->order);
			$fKModels = $model->findAll($criteria);

			// if some models founds
			if(!empty($fKModels))
			{
				$out = array();
				$primaryKey = $model->tableSchema->primaryKey;
				foreach ($fKModels as $p)
				{
					eval("\$value=$display;");
					$out[] = array(
						// expression to give the string for the autoComplete drop-down
						'label' => $value,  
						'value' => $value, 
						// return value from autocomplete
						'id' => $p->$primaryKey, 
					);
				}
				echo CJSON::encode($out);
				Yii::app()->end();
			}
		}
	}

/*	public static function getRelationFromKey($key, &$fKModelType, &$field)
	{
// TODO this line just to get past pre php 5.3 - change to fKModelType::model() once >= 5.3
//eval('$model = '.$fKModelType.'::model();');
		$model = $fKModelType::model();
		if(!is_numeric($key))
		{
			// ensure the foreign key exists in this model
			if(!in_array($key, $model->tableSchema->columnNames))
				return;

			// extract from the models relations the foreign key table
			foreach($model->relations() as $relName => $relation)
				if($relation[2] == $key)
					// $relName now contains the correct relation name which is also the object name
// TODO: ensure the lookup column exists in foreign key table
					return $relName;
		}
		// ensure the column exists
		elseif(!in_array($field, $model->tableSchema->columnNames))
			throw new CException("$field does not exist in $fKModelType ".
				print_r($$model->tableSchema->columnNames));
	}*/
	
    public function behaviors()
    {
        return array(
            'eexcelview'=>array(
                'class'=>'ext.eexcelview.EExcelBehavior',
            ),
        );
    }

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('admin','index','view','fkautocomplete'),
				'roles'=>array($this->modelName.'Read'),
			),
			array('allow',
				'actions'=>array('create','delete','update'),
				'roles'=>array($this->modelName),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function getOperations()
	{
		$operations = NULL;
		
		if(count($this->menu))
		{
			$operations = array(
				'class'=>'bootstrap.widgets.BootMenu',
				'items'=>array(
					array('label'=>'Operations', 'url'=>'#', 'items'=>$this->menu),
				),
			);
		}
		
		return $operations;
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin($exportColumns = array())
	{
		// set the message on how to use the admin screen
		Yii::app()->user->setFlash('info', self::messageSortSearch);
		
		$class = ucfirst($this->id);
		$model=new $class('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$class]))
			$model->attributes=$_GET[$class];
		if(isset($_POST[$class]))
			$model->attributes=$_POST[$class];

		// if exporting to xl
		if(isset($_POST['yt0']) && $_POST['yt0'] == 'Download Excel')
			// Export it
			$this->toExcel($model->findAll($model->searchCriteria), $exportColumns, null, array(), 'CSV'/*'Excel5'*/);
// TODO excel5 has issue on isys server likely caused by part of phpexcel wanting access to /tmp but denied		
// TODO excel2007 best format however mixed results getting succesfull creations with this = varies across servers likely php_zip issue	thnk
// it works on windows machine however not mac nor linux for me so far.

		$this->breadcrumbs = $this->getBreadCrumbTrail();

		$this->menu=array(
			array('label'=>'Create '.$this->modelName,'url'=>array('create')),
		);

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	private function multidimensional_arraySearch(&$array, &$search, $level = 0)
	{
		static $array_keys = array();
		
		// loop thru this level
		foreach($array as $key => &$value)
		{
			// if $key is int then value is not an array
			if(is_int($key))
			{
				// do we have a match
				if($search == strval($value))
				{
					$array_keys[$level] = $value;
					break;
				}
			}
			// otherwise key is not int therefore must be array
			else
			{
				// do we have a match
				if($search == strval($key))
				{
					$array_keys[$level] = $key;
					break;
				}
				// otherwise recurse if array
				elseif(is_array($value))
				{
					$this->multidimensional_arraySearch($value, $search, $level + 1);
				}
			}
			// if we have found our answer but havn't yet stored this level
			if(count($array_keys) && !isset($array_keys[$level]))
			{
				// store this level
				$array_keys[$level] = $key;
			}
		}
		
		// return the array keys array
		return $array_keys;
	} 
	
	/**
	 * Get the breadcrumb trail for this controller.
	 * return array bread crumb trail for this controller
	 */
	public function getBreadCrumbTrail($lastCrumb = NULL)
	{
		$breadcrumbs = array();
		
		// get the trail array for this model
		$trail = $this->multidimensional_arraySearch($this->trail, $this->modelName);
		
		// build the breadcrumb trail from the trail array
		for($cntr = 1; $cntr <= ($trailLength = count($trail)); $cntr++)
		{
			// if last crumb
			if($cntr == $trailLength && !$lastCrumb)
				$breadcrumbs[] = 'Manage '.$trail[$cntr - 1].'s';
			else
				$breadcrumbs['Manage '.$trail[$cntr - 1].'s'] = array("{$trail[$cntr - 1]}/index");
		}
		
		if($lastCrumb)
			$breadcrumbs[] = $lastCrumb;
		
		return $breadcrumbs;
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->breadcrumbs = $this->getBreadCrumbTrail();

		$this->menu=array(
			array('label'=>'Create '.$this->modelName,'url'=>array('create')),
			array('label'=>'Update '.$this->modelName,'url'=>array('update','id'=>$model->id)),
			array('label'=>'Delete '.$this->modelName,'url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
			array('label'=>$this->modelName.'s','url'=>array('admin')),
		);

		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new $this->modelName;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			if($model->dbCallback('save'))
			{
				$this->redirect(array('update','id'=>$model->getPrimaryKey()));
			}
		}

		$this->breadcrumbs = $this->getBreadCrumbTrail('Create');

//		$this->menu=array(
//			array('label'=>$this->modelName.'s','url'=>array('index')),
//		);

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			if($model->dbCallback('save'))
			{
				$this->redirect(array('admin','id'=>$model->getPrimaryKey()));
			}
		}

		$this->breadcrumbs = $this->getBreadCrumbTrail('Update');

		$this->menu=array(
//			array('label'=>$this->modelName.'s','url'=>array('index')),
			array('label'=>'Create '.$this->modelName,'url'=>array('create')),
//			array('label'=>'View '.$this->modelName,'url'=>array('view','id'=>$model->id)),
		);

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->actionAdmin();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$modelName = $this->modelName;
		$model=$modelName::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']===$modelName.'-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	static function listWidgetRow($model, $form, $fkField, $htmlOptions = array())
	{
		static::autoTextWidget($model, $form, $fkField, $htmlOptions);
//		static::dropDownListWidget($model, $form, $fkField, $htmlOptions);
	}
	
	static function autoTextWidget($model, $form, $fkField, $htmlOptions = array())
	{
		// get relation name from foreign key
		// TODO rewrite this as single preg_replace - use camelize function
		// upper case first letter of words other than first, remove _ and id off end
		$relName = preg_replace('/(.*)[iI]d$/', '$1', lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $fkField)))));
			
		Yii::app()->controller->widget('WMEJuiAutoCompleteFkField',
			array(
				'model'=>$model,
				'form'=>$form,
				'relName'=>$relName,
				'fkField'=>$fkField,
				'htmlOptions'=> $htmlOptions,
			)
		);
	}
	
	static function dropDownListWidget($model, $form, $fkField, $htmlOptions = array())
	{
		$modelName = str_replace('Controller', '', get_called_class());
		$target = new $modelName;
		
		echo $form->dropDownListRow(
			$target,
			$target->tableSchema->primaryKey, $modelName::getListData(),
				$htmlOptions + array(
					'class'=>'span5',
					'name'=>get_class($model)."[$fkField]"));
	}

}