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
//	public $menu=array();
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
	 * @var array the view heading
	 */
	public $heading;
	/**
	 * @var array the tab menu itemse
	 */
	private $_tabs = array();
	/**
	 * @var bool whether to show the new button in the admin  
	 */
	protected $_adminShowNew = true;
	
	/**
	 * @var string the flash message to show sort and search instructions
	 */
	const messageSortSearch = '<p><strong>To sort,</strong> click on column name.
		<p><strong>To search,</strong> enter part of any term and click elsewhere.
		/ in a column heading means you can search the different parts by seperating with /.';
	/**
	 * @var string the flash message to show sort and search adn compare instructions
	 */
	const messageSortSearchCompare = "<p><strong>To sort,</strong> click on column name.
		<p><strong>To search,</strong> enter part of any term and click elsewhere.
		/ in a column heading means you can search the different parts by seperating with /.
		<p>You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>,
		<b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values.";
	/**
	 * @var array provides the hierachy for a breadcrumb trail
	 */
	private $trail = array(
		'Client'=>array(
			'Project'=>array( 
				'Task'=>array(
					'Duty',
					'Reschedule',
// TODO: naming orientation is inconsistent here
					'MaterialToTask',
					'TaskToAssembly',
					'TaskToGenericTaskType',
					'TaskToResourceType',
				),
				'ProjectToProjectTypeToAuthItem',
				'ProjectToGenericProjectType'
			),
			'ProjectType'=>array(
				'ProjectTypeToAuthItem',
				'GenericProjectType',
				'TaskType'=>array(
					'GenericTaskType',
					'TaskTypeToDutyType',
				),
			),
		),
		'DefaultValue',
		'Dutycategory'=>array(
			'DutyType',
		),
		'Resourcecategory'=>array(
			'ResourceType',
		),
		'GenericType',
		'Genericprojectcategory',
		'Generictaskcategory',
		'PurchaseOrder',
		'Assembly',
		'Material',
		'Staff'=>array(
			'AuthAssignment',
		),
		'AuthItem'=>array(
			'AuthItemChild',
		),
	);
	
	public function __construct($id, $module = null)
	{
		$this->modelName = str_replace('Controller', '', get_class($this));
		
		// clear the labelOverrides that may have been used in previous view
		ActiveRecord::$labelOverrides = array();
		
		// set up the buttons for use in admin view
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
	public function actionAutocomplete()
	{
		// if something has been entered
		if (isset($_GET['term']))
		{
			// url parameters
			$modelName = /*$_GET['fk_model']*/ $this->modelName;
			$model = $modelName::model();
			// protect against possible injection
			$criteria = new CDbCriteria;
			$criteria->params = array();
			$terms = explode(Yii::app()->params['delimiter']['search'], $_GET['term']);

			// key will contain either a number or a foreign key field in which case field will be the lookup value
			foreach($modelName::getDisplayAttr() as $key => $field)
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
			$criteria->scopes = empty($_GET['scopes']) ? null : $_GET['scopes'];
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
				'actions'=>array('admin','index','view'),
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

	public function getTabs()
	{
		return $this->_tabs;
	}

	/**
	 * _tabs property setter. Utilises trail to set tab options.
	 * @param ActiveRecord $model the model to extract primary key information from to build query string
	 * @param $nextLevel if true uses next level down in trail array from the models level if false uses the models level
	 * to extract tab options. Admin and create use models level, update uses next level unless
	 * the current model is a leaf rather than branch (no further branching) in which case popup form will be used to update
	 */
	public function setTabs($model, $nextLevel = true)
	{
		// multidimensional array search returns from start of search array to target
		// need to get the next level items from that point only

		// step thru the trail to our target
		$items = $this->trail;
		// if we should return this level NB: this is empty deliberately to keep condition the same as below
		if(!$nextLevel && (isset($items[$this->modelName])))
		{
		}
		else
		{
			$trail = $this->multidimensional_arraySearch($this->trail, $this->modelName);
			// get tree of items at or below the desired level
			foreach($trail as $key => &$value)
			{
				// if we should return this level
				if(!$nextLevel && (isset($items[$this->modelName]) || in_array($this->modelName, $items)))
				{
					break;
				}
				$items = $items[$value];
			}
			// NB: by now items could be empty if looking for next level and nothing below i.e. next level from leaf
			
			// if there are items
			if($items) 
			{
				if($nextLevel) // true for create and update
				{
					// still want to show this model in tabs
					array_unshift($items, $this->modelName);
				}
				// otherwise
				else // admin
				{
					if(sizeof($trail) > 1)
					{
						// want to show the parent model first in tabs so add to top of items
						array_unshift($items, $trail[sizeof($trail) - 2]);
					}
				}
			}
			else
			{
				$items = array($this->modelName);
			}
		}
		
		// if there are items
		if(is_array($items))
		{
			$index = 0;
			// carry the important ids for breadcrumbs
			$get = isset($_GET[$this->modelName]) ? $_GET[$this->modelName] : $_GET;
			$keyValue = each($get);
			$keyValue = $keyValue['value'];

			foreach($items as $key => &$value)
			{
				// get the model name of this item
				$modelName = is_array($value) ? $key : $value;
				
				// check access
				if(!$this->checkAccess(self::accessRead))
				{
					continue;
				}
				
				// if this item matches the main model
				if($modelName == $this->modelName)
				{
					// make this the active tab
					$this->_tabs[$index]['active'] = true;
				}
				
				// if first item
				if(!$index)
				{
					// store this (first tabs model name)
					$firstTabModelName = $modelName;
					
					// if nextlevel is true then action should always be update, but also should be update if current model is this model
					// and not next level

					// create controler/action
					if($keyValue && (!$nextLevel || ($modelName == $this->modelName)))
					{
						$this->_tabs[$index]['label'] = $modelName::getNiceName($keyValue);
						$thisModel = new $modelName;
						$this->_tabs[$index]['url'] = array("$modelName/update", 'id'=>$keyValue);
						$index++;
						continue;
					}
				}
				
				// add relevant url parameters i.e. foreign key to first tab model
				$urlParams = ($keyValue === null)
					? array()
					: array($modelName => array($modelName::getParentForeignKey($firstTabModelName) => $keyValue));
				
				$this->_tabs[$index]['label'] = $modelName::getNiceName() . 's';
				$this->_tabs[$index]['url'] =  array("$modelName/admin") + $urlParams;
				$index++;
			}
		}
	}

	public function getAllMenu(&$array = null, $level = 0)
	{
		static $items = array();
		
		// if starting this recursive function
		if(!$level)
		{
			// reset the static variable from the last time this was called
			$items = array();
			// set the initial value of the array to the trail
			$array = $this->trail;
		}

		// loop thru this level
		foreach($array as $key => &$value)
		{
			// if $value is an array
			if(is_array($value))
			{
				// store key if user has access and not first level which they already have access to so don't repeat
				if($level && $this->checkAccess(self::accessRead))
				{
					$items[] = array('label'=>$key::getNiceName() . 's', 'url'=>Yii::app()->createUrl("$key/admin"));
				}
				// recurse
				$this->getAllMenu($value, $level + 1);
			}
			// otherwise value is not an array
			else
			{
				// store value if user has access and not first level which they already have access to so don't repeat
				if($level && $this->checkAccess(self::accessRead))
				{
					$items[] = array('label'=>$value::getNiceName() . 's', 'url'=>Yii::app()->createUrl("$value/admin"));
				}
			}
		}
		
		// if we are not recursing and have items
		if(!$level && count($items))
		{
			return array(
					'class'=>'bootstrap.widgets.TbMenu',
					'items'=>array(
						array('label'=>'All', 'url'=>'#', 'items'=>$items),
					),
				);
		}
		
		// return the array keys array
		return array();
	}

/*	public function getOperations()
	{
		$operations = array();
		
		if(count($this->menu))
		{
			$operations = array(
				'class'=>'bootstrap.widgets.TbMenu',
				'items'=>array(
					array('label'=>'Operations', 'url'=>'#', 'items'=>$this->menu),
				),
			);
		}
		
		return $operations;
	}*/

	/**
	 * Manages all models.
	 */
	public function actionAdmin($exportColumns = array())
	{
		// set the message on how to use the admin screen
		Yii::app()->user->setFlash('info', self::messageSortSearch);

		$modelName = /*ucfirst($this->id)*/$this->modelName;

		// NB: query string is stripped from ajaxUrl hence this hack, but also used
		// in building breadcrumbs
		if(isset($_GET['ajax']))
		{
			// restore $_GET
			if(!isset($_GET[$modelName]))
			{
				$_GET[$modelName] = array();
			}
			$_GET[$modelName] += isset($_SESSION['actionAdminGet'][$modelName]) ? $_SESSION['actionAdminGet'][$modelName] : array();
		}
		elseif(isset($_GET[$modelName]))
		{
			// store $_GET
			$_SESSION['actionAdminGet'][$modelName] = $_GET[$modelName];
		}
		else
		{
			// loose our memory
			unset($_SESSION['actionAdminGet']);
			// initialise as we check for this presence in defining breadcrumbs to know we have been thru this admin view
			$_SESSION['actionAdminGet'][$modelName] = array();
			// block display of the new button unless top level of trail
			if(sizeof($this->multidimensional_arraySearch($this->trail, $this->modelName)) > 1)
			{
				// should we be showing the new button
				$this->_adminShowNew = false;
			}
		}
		
		$model=new $modelName('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$modelName]))
			$model->attributes=$_GET[$modelName];
		if(isset($_POST[$modelName]))
			$model->attributes=$_POST[$modelName];

		// if exporting to xl
		if(isset($_POST['yt0']) && $_POST['yt0'] == 'Download Excel')
			// Export it
			$this->toExcel($model->findAll($model->searchCriteria), $exportColumns, null, array(), 'CSV'/*'Excel5'*/);
// TODO excel5 has issue on isys server likely caused by part of phpexcel wanting access to /tmp but denied		
// TODO excel2007 best format however mixed results getting succesfull creations with this = varies across servers likely php_zip issue	thnk
// it works on windows machine however not mac nor linux for me so far.

		// set heading
		if(!$this->heading)
		{	
			$this->heading .= $modelName::getNiceName() . 's';
		}

		// set breadcrumbs
		$this->breadcrumbs = $this->getBreadCrumbTrail();

		// set up tab menu if required - using setter
		$this->setTabs($model, false);
		
		$this->render('/admin',array(
			'model'=>$model,
		));
	}

	private function multidimensional_arraySearch(&$array, &$search, $level = 0)
	{
		static $array_keys = array();
		
		// if starting this recursive function
		if(!$level)
		{
			// reset the static variable from the last time this was called
			// could alternatly store the search value and only scan once if the
			// same
			$array_keys = array();
		}

		// loop thru this level
		foreach($array as $key => &$value)
		{
			// if $key is not an array
			if(!is_array($value))
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
				break;
			}
		}
		
		// if we are exiting and not recursing
		if(!$level)
		{
			// sort by the arrays ascending so that we know we have the write order in foreach loops
			ksort($array_keys);
		}
		
		// return the array keys array
		return $array_keys;
	} 

	/**
	 * Determine if a particular primary key exists in the breadcrumb trail - in any model.
	 * @param string $primaryKey the primary key attribute name
	 * @return bool true if primary key is in breadcrumbs otherwise false
	 */
	public function primaryKeyInBreadCrumbTrail($primaryKey)
	{
		// loop thru the trail for this model
		foreach($t=$this->multidimensional_arraySearch($this->trail, $this->modelName) as $crumb)
		{
			// see if any query paramters
			if($queryParamters = (!empty($_SESSION['actionAdminGet'][$crumb]) ? $_SESSION['actionAdminGet'][$crumb] : null))
			{
				// if primary key exists
				if(!empty($queryParamters[$primaryKey]))
				{
					return true;
				}
			}
		}
	}
	
	/**
	 * Get the breadcrumb trail for this controller.
	 * return array bread crumb trail for this controller
	 */
	public function getBreadCrumbTrail($lastCrumb = NULL)
	{
		$breadcrumbs = array();
	
		// if just gone direct to a screen i.e. our memory/history was cleared
		if(!isset($_SESSION['actionAdminGet']) && !$lastCrumb)
		{
			if(Yii::app()->user->checkAccess("{$this->modelName}Read"))
			{
				$breadcrumbs[] = $this->modelName;
			}
			return $breadcrumbs;
		}

		// loop thru the trail for this model
		foreach($this->multidimensional_arraySearch($this->trail, $this->modelName) as $crumb)
		{
			// check access
			if(!Yii::app()->user->checkAccess("{$crumb}Read"))
			{
				continue;
			}
			// otherwise if we haven't come via this route
			elseif(!isset($_SESSION['actionAdminGet'][$crumb]))
			{
				continue;
			}

			// see if any query paramters
			$queryParamters = !empty($_SESSION['actionAdminGet'][$crumb]) ? array($crumb=>$_SESSION['actionAdminGet'][$crumb]) : array();

			$display = $crumb::getNiceName();
			// if this is the last crumb
			if($this->modelName == $crumb)
			{
				if($lastCrumb == 'Create')
				{
					// add crumb to admin view
					$breadcrumbs[$display.'s'] = array("$crumb/admin");
					// add last crumb
					$breadcrumbs[] = $lastCrumb;
				}
				elseif($lastCrumb == 'Update')
				{
					// add crumb to admin view. NB using last query paramters to that admin view
					$breadcrumbs[$display.'s'] = array("$crumb/admin") + $queryParamters;
					// add an update crumb to this primary key
					$primaryKey = $_SESSION[$crumb];
					$breadcrumbs[] = $crumb::getNiceName($primaryKey['value']);
				}
				else
				{
					$breadcrumbs[] = $display.'s';
				}
			}
			// otherwise not last crumb
			else
			{
				// add crumb to admin view
				$breadcrumbs[$display.'s'] = array("$crumb/admin") + $queryParamters;
			
				// if there is a primary key for this
				if(isset($_SESSION[$crumb]))
				{
					// add an update crumb to this primary key
					$primaryKey = $_SESSION[$crumb];
					$breadcrumbs[$crumb::getNiceName($primaryKey['value'])] = array("$crumb/update", 'id'=>$primaryKey['value']);
				}
			}
		}

		return $breadcrumbs;
	}

	/*
	 * to be overidden if using mulitple models
	 */
	protected function createSave($model, $models)
	{
		return $model->dbCallback('save');
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new $this->modelName;

		// $validating will be set to true if ajax validating and passed so-far but still need to try, catch db errors before actual submit
		$validating =$this->performAjaxValidation($model);
// TODO: this is untested without javascript

		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			
			// start a transaction
			$transaction = Yii::app()->db->beginTransaction();
			
			// attempt save
			$saved = $this->createSave($model, $models);

			// if not validating and successful
			if(!$validating && $saved)
			{
				// commit
                $transaction->commit();
				$this->redirect(array('update', 'id'=>$model->getPrimaryKey()));
			}
			// otherwise there has been an error which should be captured in model
			else
			{
				// rollback
                $transaction->rollBack();
				// if coming from ajaxvalidate
				if($validating)
				{
					$result=array();
					if(!is_array($models)) 
					{
						$models=array($model);
					}
					foreach($models as $model)
					{
						foreach($model->getErrors() as $attribute=>$errors)
						{
							$result[CHtml::activeId($model,$attribute)]=$errors;
						}
					}
					// return the json encoded data to the client
					echo $t = function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
					Yii::app()->end();
				}

				$model->isNewRecord = TRUE;
			}
		}
		elseif(isset($_GET[$this->modelName]))
		{
			// set any url based paramters
			$model->attributes=$_GET[$this->modelName];
		}

		// add primary key into session so it can be retrieved for future use in breadcrumbs
		$_SESSION[$this->modelName] = array(
			'name'=>$model->tableSchema->primaryKey,
			'value'=>$id,
		);
		

		$this->widget('CreateViewWidget', array(
			'model'=>$model,
			'models'=>$models,
		));
	}

// TODO: huge duplication between actionUpdate and actionCreate - remove duplication 	
	
	/*
	 * to be overidden if using mulitple models
	 */
	protected function updateSave($model, $models)
	{
		return $model->dbCallback('save');
	}
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'update' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// $validating will be set to true if ajax validating and passed so-far but still need to try, catch db errors before actual submit
		$validating =$this->performAjaxValidation($model);
// TODO: this is untested without javascript

		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			
			// start a transaction
			$transaction = Yii::app()->db->beginTransaction();
			
			// attempt save
			$saved = $this->updateSave($model, $models);

			// if not validating and successful
			if(!$validating && $saved)
			{
				// commit
                $transaction->commit();
				$this->redirect(array('admin'));
			}
			// otherwise there has been an error which should be captured in model
			else
			{
				// rollback
                $transaction->rollBack();
				// if coming from ajaxvalidate
				if($validating)
				{
					$result=array();
					if(!is_array($models)) 
					{
						$models=array($model);
					}
					foreach($models as $model)
					{
						foreach($model->getErrors() as $attribute=>$errors)
						{
							$result[CHtml::activeId($model,$attribute)]=$errors;
						}
					}
					// return the json encoded data to the client
					echo $t = function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
					Yii::app()->end();
				}

				$model->isNewRecord = TRUE;
			}
		}

		// add primary key into session so it can be retrieved for future use in breadcrumbs
		$_SESSION[$this->modelName] = array(
			'name'=>$model->tableSchema->primaryKey,
			'value'=>$id,
		);
		
		// otherwise this is just a get and could be passing paramters
		$model->attributes=$_GET[$this->modelName];
		
		// set heading
		$modelName = $this->modelName;
		$this->heading = $modelName::getNiceName($id);

		// set breadcrumbs
		$this->breadcrumbs = $this->getBreadCrumbTrail('Update');
		
		// set up tab menu if required - using setter
		$this->tabs = $model;

		$this->widget('UpdateViewWidget', array(
			'model'=>$model,
			'models'=>$models,
		));
	}


	/**
	 * Views a particular model.
	 * @param integer $id the ID of the model to be viewed
	 */
	public function actionView($id)
	{
		$model=$this->loadModel($id);

		$this->widget('UpdateViewWidget', array(
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
			try
			{
				// we only allow deletion via POST request
				$model = $this->loadModel($id);

				// if this model has a deleted attribute
				if(isset($model->deleted))
				{
					// mark the row as deleted
					$model->deleted = true;
					$model->save();
				}
				// otherwise delete the row
				else
				{
					$model->delete();
				}
				
				if(!isset($_GET['ajax']))
				{
					Yii::app()->user->setFlash('error','<strong>Success!</strong>
						The row has been succesfully deleted.');
				}
				else
				{
					echo "
						<div class='alert alert-block alert-error fade in'>
							<a class='close' data-dismiss='alert'>×</a>
							<strong>Success!</strong>
							The row has been succesfully deleted.
						</div>
					";
				}
  			}
			catch (CDbException $e)
			{
				if(!isset($_GET['ajax']))
				{
					Yii::app()->user->setFlash('error','<strong>Oops!</strong>
						Unfortunately you can&#39;t delete this as at least one other record in the database refers to it.');
				}
				else
				{
					echo "
						<div class='alert alert-block alert-error fade in'>
							<a class='close' data-dismiss='alert'>×</a>
							<strong>Oops!</strong>
							Unfortunately you can&#39;t delete this as at least one other record in the database refers to it.
						</div>
					";
				}
			}
			
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
			{
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		}
		else
		{
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		// NB: need to redirect as opposed to just calling the relavant action so that the url is correct base
		// form form action on the admin view
		$this->redirect(array('admin'));
		//$this->actionAdmin();
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
		$validating = false;
		if(isset($_POST['ajax']) && $_POST['ajax']===$this->modelName.'-form')
		{
			$jsonErrors = CActiveForm::validate($model);
			if($model->hasErrors())
			{
				echo $jsonErrors;
				Yii::app()->end();

			}
			$validating = true;
		}

		return $validating;
	}
	
	static function listWidgetRow($model, $form, $fkField, $htmlOptions = array(), $scopes = array())
	{
		// set any required default scope
		// NB this only applies here to drop down list which is populated now, otherwise scope needs to be passed to parent autocomplete
		// via child autocomplete - warning, the alias can be confusing
// TODO: should be able to set these scopes in one place and derive correct alias if alias needed

		static::autoTextWidget($model, $form, $fkField, $htmlOptions, $scopes);
//		static::dropDownListWidget($model, $form, $fkField, $htmlOptions);
//		static::$defaultScope = $defaultScope;
	}
	
	static function autoTextWidget($model, $form, $fkField, $htmlOptions, $scopes)
	{
//		// get relation name from foreign key
//		$relName = preg_replace('/(.*)[iI]d$/', '$1', Yii::app()->functions->camelize($fkField));
		
		Yii::app()->controller->widget('WMEJuiAutoCompleteFkField',
			array(
				'model'=>$model,
				'form'=>$form,
//				'relName'=>$relName,
				'fkField'=>$fkField,
				'htmlOptions'=>$htmlOptions,
				'scopes'=>$scopes,
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
					'name'=>get_class($model)."[$fkField]"));
	}

	const accessRead = 'Read';
	const accessWrite = '';
	public function checkAccess($mode, $modelName=null)
	{
		if($mode == self::accessRead || $mode === self::accessWrite)
		{
			return Yii::app()->user->checkAccess(($modelName ? $modelName : $this->modelName) . $mode);
		}
// TODO: throw error i.e. invalid $mode for
	}
	
	
}
?>