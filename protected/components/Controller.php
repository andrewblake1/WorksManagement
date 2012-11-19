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
	 * @var array the view heading
	 */
	public $heading;
	/**
	 * @var array the tab menu itemse
	 */
	protected $_tabs = array();
/**
	* @var bool whether to show the new button in the admin  
protected $_adminShowNew = false;
	*/
	/**
	 * @var string the name of the model to use in the admin view - the model may serve a database view as opposed to a table  
	 */
	protected $_adminViewModel;
	/**
	 * @var string the name of the admin view
	 */
	protected $_adminView = '/admin';
	
	/**
	 * @var string the flash message to show sort and search instructions
	 */
//	const messageSortSearch = '<p><strong>To sort,</strong> click on column name.
//		<p><strong>To search,</strong> enter part of any term and click elsewhere.
//		/ in a column heading means you can search the different parts by seperating with /.';
	/**
	 * @var string the flash message to show sort and search adn compare instructions
	 */
//	const messageSortSearchCompare = "<p><strong>To sort,</strong> click on column name.
//		<p><strong>To search,</strong> enter part of any term and click elsewhere.
//		/ in a column heading means you can search the different parts by seperating with /.
//		<p>You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>,
//		<b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values.";
	
	static function modelName()
	{
		return str_replace('Controller', '', get_called_class());
	}
	
	public function __construct($id, $module = null)
	{
		$this->modelName = static::modelName();
/*try {

}
Yii::app()->dbReadOnly->createCommand('select * from AuthItem')->queryAll();*/	
		if(empty($this->_adminViewModel))
		{
			$this->_adminViewModel = $this->modelName;
		}
		
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

	// data provider for EJuiAutoCompleteFkField
	public function actionAutocomplete()
	{
		// if something has been entered
		if (isset($_GET['term']))
		{
			$modelName = /*$_GET['fk_model']*/ $this->modelName;
	
			// protect against possible injection
			$criteria = new CDbCriteria;
			$concat = array();
				
			foreach($modelName::getDisplayAttr() as $field)
			{
				// building display parameter which gets eval'd later
				$display[] = '{$p->'.$field.'}';

				/*
				* $matches[5] attribute
				* $matches[4] alias
				* $matches[1] relations
				*/
				if(preg_match('/(((.*)->)?(\w*))->(\w*)$/', $field, $matches))
				{
					$criteria->with[] = $matches[1];
					$alias = $matches[4];
					$attribute = $matches[5];
				}
				else
				{
					$alias = 't';
					$attribute = $field;
				}

				$criteria->order[] = "$alias.$attribute ASC";
				$concat[] = "$alias.$attribute";
			}

			// create the search term
			$concat = "CONCAT_WS(' ', ". implode(', ', $concat) . ")";
			$cntr = 0;
			$criteria->params = array();
			foreach($terms = explode(' ', $_GET['term']) as $term)
			{
				$term = trim($term);
				$paramName = ":param$cntr";
				$criteria->condition .= ($criteria->condition ? " AND " : '')."$concat LIKE $paramName";
				$criteria->params[$paramName] = "%$term%";
				$cntr++;
			}

			// limit the results
			$criteria->limit = 20;
			$criteria->order = implode(', ', $criteria->order);
			$display = implode(Yii::app()->params['delimiter']['display'], $display);
			$criteria->scopes = empty($_GET['scopes']) ? null : $_GET['scopes'];
			$model = $modelName::model();
			$fKModels = $model->findAll($criteria);

			// if some models founds
			if(!empty($fKModels))
			{
				$out = array();
				$primaryKey = $model->tableSchema->primaryKey;
				foreach ($fKModels as $p)
				{
					eval("\$value=\"$display\";");
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
	
    public function behaviors()
    {
        return array(
            'eexcelview'=>array(
                'class'=>'ext.eexcelview.EExcelBehavior',
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
		$items = Yii::app()->params['trail'];
		// if we should return this level NB: this is empty deliberately to keep condition the same as below
		if(!$nextLevel && (isset($items[$this->modelName])))
		{
		}
		else
		{
			$trail = Yii::app()->functions->multidimensional_arraySearch(Yii::app()->params['trail'], $this->modelName);
			// get tree of items at or below the desired level
			foreach($trail as $key => &$value)
			{
				// if we should return this level
				if(!$nextLevel && (isset($items[$this->modelName]) || in_array($this->modelName, $items)))
				{
					break;
				}
				// if there are items below
				$items = isset($items[$value]) ? $items[$value] : null;
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
			if(is_array($get))
			{
				$keyValue = each($get);
				$keyValue = $keyValue['value'];
			}
			else
			{
				$keyValue = null;
			}

			foreach($items as $key => &$value)
			{
				// get the model name of this item
				$modelName = is_array($value) ? $key : $value;
				
				// check access
				if(!static::checkAccess(self::accessRead, $modelName))
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
						$this->_tabs[$index]['url'] = array("$modelName/update", 'id'=>$keyValue);
						$index++;
						continue;
					}
				}
				
				// add relevant url parameters i.e. foreign key to first tab model
				$urlParams = ($keyValue === null)
					? array()
					: array($modelName => array($modelName::getParentForeignKey($firstTabModelName) => $keyValue));
				
				$this->_tabs[$index]['label'] = $modelName::getNiceNamePlural();
				$this->_tabs[$index]['url'] =  array("$modelName/admin") + $urlParams;
				$index++;
			}
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin($exportColumns = array())
	{
		// set the message on how to use the admin screen
//		Yii::app()->user->setFlash('info', self::messageSortSearch);

		$modelName = $this->modelName;

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
			// store $_GET
			$_SESSION['actionAdminGet'][$modelName] = null;
		}
		
		// may be using a database view instead of main table model
		$adminViewModel = $this->_adminViewModel;
		$model=new $adminViewModel('search');
		
		$model->unsetAttributes();  // clear any default values
		$attributes = array();
		if(!empty($_GET[$this->_adminViewModel]))
		{
			$attributes += $_GET[$this->_adminViewModel];
		}
		if(!empty($_GET[$modelName]))
		{
			$attributes += $_GET[$modelName];
		}
		if(!empty($_POST[$this->_adminViewModel]))
		{
			$attributes += $_POST[$this->_adminViewModel];
		}
		if(!empty($_POST[$modelName]))
		{
			$attributes += $_POST[$modelName];
		}
		$model->attributes = $attributes;

		// ensure that where possible a pk has been passed from parent
		$model->assertFromParent();
		
		// if exporting to xl
		if(isset($_GET['action']) && $_GET['action'] == 'download')
		{
			// Export it
			$this->toExcel($model->findAll($model->searchCriteria), $exportColumns, null, array(), 'CSV'/*'Excel5'*/);
		}
// TODO excel5 has issue on isys server likely caused by part of phpexcel wanting access to /tmp but denied		
// TODO excel2007 best format however mixed results getting succesfull creations with this = varies across servers likely php_zip issue	thnk
// it works on windows machine however not mac nor linux for me so far.

		// set heading
		if(!$this->heading)
		{	
			$this->heading .= $modelName::getNiceNamePlural();
		}

		// set breadcrumbs
		$this->breadcrumbs = $this->getBreadCrumbTrail();

		$this->adminRender($model);
	}

	protected function adminRender($model)
	{
		// set up tab menu if required - using setter
		$this->setTabs($model, false);

		$this->render(lcfirst($this->_adminView), array(
			'model'=>$model,
		));
	}
	
	/**
	 * Determine if a particular primary key exists in the breadcrumb trail - in any model.
	 * @param string $primaryKey the primary key attribute name
	 * @return bool true if primary key is in breadcrumbs otherwise false
	 */
	public function primaryKeyInBreadCrumbTrail($primaryKey)
	{
		// loop thru the trail for this model
		foreach(Yii::app()->functions->multidimensional_arraySearch(Yii::app()->params['trail'], $this->modelName) as $crumb)
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
	
	
	public function getParentCrumb($modelName = null)
	{
		if(empty($modelName))
		{
			$modelName = $this->modelName;
		}

		$trail = array_reverse(Yii::app()->functions->multidimensional_arraySearch(Yii::app()->params['trail'], $modelName));

		if(!empty($trail[1]))
		{
			return $trail[1];
		}
	}
	/**
	 * Get the breadcrumb trail for this controller.
	 * return array bread crumb trail for this controller
	 */
	public function getBreadCrumbTrail($lastCrumb = NULL)
	{
		$breadcrumbs = array();
		$modelName = $this->modelName;
	
		// if just gone direct to a screen i.e. our memory/history was cleared
		if(!isset($_SESSION['actionAdminGet']) && !$lastCrumb)
		{
			if(static::checkAccess(self::accessRead))
			{
				$breadcrumbs[] = $modelName;
			}
			return $breadcrumbs;
		}

		// loop thru the trail for this model
		foreach(Yii::app()->functions->multidimensional_arraySearch(Yii::app()->params['trail'], $modelName) as $crumb)
		{
			// check access
			if(!static::checkAccess(self::accessRead, $crumb))
			{
				continue;
			}

			// see if any query paramters
			$queryParamters = !empty($_SESSION['actionAdminGet'][$crumb]) ? array($crumb=>$_SESSION['actionAdminGet'][$crumb]) : array();

			$display = $crumb::getNiceName();
			$displays = $crumb::getNiceNamePlural();
			// if this is the last crumb
			if($modelName == $crumb)
			{
				if($lastCrumb == 'Create')
				{
					// add crumb to admin view
					$breadcrumbs[$displays] = array("$crumb/admin") + $queryParamters;
					// add last crumb
					$breadcrumbs[] = $lastCrumb;
				}
				elseif($lastCrumb == 'Update')
				{
					// add crumb to admin view. NB using last query paramters to that admin view
					$breadcrumbs[$displays] = array("$crumb/admin") + $queryParamters;
					// add a crumb with just the primary key nice name but no href
					$primaryKey = $_SESSION[$crumb];
					$breadcrumbs[] = $crumb::getNiceName($primaryKey['value']);
				}
				else
				{
					$breadcrumbs[] = $displays;
				}
			}
			// otherwise not last crumb
			else
			{
				// add crumb to admin view
				$breadcrumbs[$displays] = array("$crumb/admin") + $queryParamters;
			
				// if there is a primary key for this
				if(isset($_SESSION[$crumb]))
				{
					// add an update crumb to this primary key
					$primaryKey = $_SESSION[$crumb];
		//			$breadcrumbs[$crumb::getNiceName($primaryKey['value'])] = array("$crumb/update", 'id'=>$primaryKey['value']);
					$breadcrumbs[$crumb::getNiceName($primaryKey['value'])] = array("$crumb/update", $primaryKey['name']=>$primaryKey['value']);
				}
			}
		}

		return $breadcrumbs;
	}

	/*
	 * to be overidden if using mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		// atempt save
		$saved = $model->dbCallback('save');
		// put the model into the models array used for showing all errors
		$models[] = $model;
		
		return $saved;
	}
	
	/*
	 * to be overidden if not wanting to redirect to admin
	 */
	protected function createRedirect($model)
	{
		$this->cuRedirect($model);
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($modalId = 'myModal', &$model = null)
	{
		if($model === null)
		{
			$model=new $this->modelName;
		}
		$models=array();

		// $validating will be set to true if ajax validating and passed so-far but still need to try, catch db errors before actual submit
		$validating =$this->performAjaxValidation($model);
// TODO: this is untested without javascript

		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
$t = $model->attributes;		
			// start a transaction
			$transaction = Yii::app()->db->beginTransaction();
			
			// attempt save
			$saved = $this->createSave($model, $models);

			// if not validating and successful
			if(!$validating && $saved)
			{
				// commit
                $transaction->commit();
				$this->createRedirect($model);
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
					foreach($models as $m)
					{
						foreach($m->getErrors() as $attribute=>$errorS)
						{
							$result[CHtml::activeId($m,$attribute)]=$errorS;
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

/*		// add primary key into session so it can be retrieved for future use in breadcrumbs
		$_SESSION[$this->modelName] = array(
			'name'=>$model->tableSchema->primaryKey,
			'value'=>$id,
		);*/
		
		// if just failed to save after ajax validation ok'd it - maybe an invalid file upload which can't use ajax validation
		if(isset($saved) && !$saved)
		{
			// get errors
			$message = '
				<strong>Sorry, could,\'t save because</strong>
					<ul>';
			foreach($models as $m)
			{
				foreach($m->getErrors() as $attribute=>$errors)
				{
					foreach($errors as $error)
					{
						$message.="<li>$error</li>";
					}
				}
			}
			$message .= '</ul>';
// TODO: use jquery show to show the create form on reentry and show the validation error message thefore pass the error message to createrender
// instead of flash message which is just a quick temporary solution
			Yii::app()->user->setFlash('error', $message);
			// redirect back to this view - most likely admin but pass paramter to let know about failed validation
			$this->createRedirect($model);
		}
		
		$this->createRender($model, $models, $modalId);
	}
	
	protected function createRender($model, $models, $modalId)
	{
		$this->widget('CreateViewWidget', array(
			'model'=>$model,
			'models'=>$models,
			'modalId'=>$modalId,
		));
	}

// TODO: huge duplication between actionUpdate and actionCreate - remove duplication  - and in the respective redirect and save methods
	
	/*
	 * to be overidden if using mulitple models
	 */
	protected function updateSave($model, &$models=array())
	{
		// atempt save
		$saved = $model->dbCallback('save');
		// put the model into the models array used for showing all errors
		$models[] = $model;
		
		return $saved;
	}
	
	/*
	 * to be overidden if not wanting to redirect to admin
	 */
	protected function updateRedirect($model)
	{
		$this->cuRedirect($model);
	}
	
	private function cuRedirect($model)
	{ 
		// if posted a controller then this is where we should return to
// TODO: identical to create redirect - put into private
		if(!empty($_POST['controller']))
		{
			$modelName = $_POST['controller'];
//			$this->redirect(array("$modelName/admin", $modelName=>array($_SESSION[$modelName]['name']=>$_SESSION[$modelName]['value'])));
			$this->redirect(array("$modelName/admin", $modelName=>$_SESSION['actionAdminGet'][$modelName]));
		}
		elseif(is_array($_SESSION['actionAdminGet'][$this->modelName]))
		{
//			$modelName = $this->modelName;
//			$this->redirect(array("$modelName/admin", $modelName=>array($_SESSION[$modelName]['name']=>$_SESSION[$modelName]['value'])));
			$this->redirect(array('admin', $this->modelName=>$_SESSION['actionAdminGet'][$this->modelName]));
		}
		else
		{
			$this->redirect(array('admin'));
		}
	}
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'update' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id, $model = null)
	{
		if($model === null)
		{
			$model=$this->loadModel($id);
		}
		$models=array();

		// ensure that actionadmin is set
		$model->assertFromParent();

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
				$this->updateRedirect($model);
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
					foreach($models as $m)
					{
						foreach($m->getErrors() as $attribute=>$errors)
						{
							$result[$this->actionGetHtmlId($m,$attribute)]=$errors;
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
		if(!empty($_GET[$this->modelName]))
		{
			$model->attributes=$_GET[$this->modelName];
		}
		
		// set heading
		$modelName = $this->modelName;
		$this->heading = $modelName::getNiceName($id);

		// ensure that where possible a pk has been passed from parent and get that fk name if possible
		$parent_fk = $model->assertFromParent();
		
		// set breadcrumbs
		$this->breadcrumbs = $this->getBreadCrumbTrail('Update');
		
		// set up tab menu if required - using setter
		$this->tabs = $model;

		$this->widget('UpdateViewWidget', array(
			'model'=>$model,
			'models'=>$models,
			'parent_fk'=>$parent_fk,
		));

	}

	protected function actionGetHtmlId($model,$attribute)
	{
		return CHtml::activeId($model,$attribute);
	}

	/**
	 * Views a particular model.
	 * @param integer $id the ID of the model to be viewed
	 */
// TODO: the guts of this is duplicated in actionUpdate
	public function actionView($id)
	{
		$model=$this->loadModel($id);
		$primaryKeyName = $model->tableSchema->primaryKey;
		
		// add primary key into session so it can be retrieved for future use in breadcrumbs
		$_SESSION[$this->modelName] = array(
			'name'=>$primaryKeyName,
			'value'=>$id,
		);
		
		// otherwise this is just a get and could be passing paramters
		$model->$primaryKeyName=$id;
		
		// set heading
		$modelName = $this->modelName;
		$this->heading = $modelName::getNiceName($id);

		// set breadcrumbs
		$this->breadcrumbs = $this->getBreadCrumbTrail('Update');
		
		// set up tab menu if required - using setter
		$this->tabs = $model;

		$this->widget('UpdateViewWidget', array(
			'model'=>$model,
//			'models'=>$models,
		));
	}

	protected function actionAfterDelete()
	{
		
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

/*				// if this model has a deleted attribute
				if(isset($model->deleted))
				{
					// mark the row as deleted - increment to allow re-create and re delete later without violating unique constraints combined with deleted
					$model->deleted++;
					$model->save();
				}
				// otherwise delete the row
				else
				{
					$model->delete();
				}*/
				$model->delete();
				
				// call up any special handling in child class
				$this->actionAfterDelete($model);
				
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
				$this->redirect(isset($_POST['returnUrl'])
					? $_POST['returnUrl']
					: array('admin', $this->modelName=>$_SESSION['actionAdminGet'][$this->modelName]));
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
		$this->redirect(array('admin', $this->modelName=>$_SESSION['actionAdminGet'][$this->modelName]));
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
	
	static function listWidgetRow($model, $form, $fkField, $htmlOptions = array(), $scopes = array(), $label = null)
	{
		// set any required default scope
		// NB this only applies here to drop down list which is populated now, otherwise scope needs to be passed to parent autocomplete
		// via child autocomplete - warning, the alias can be confusing
// TODO: not sure on scopes here

		
		// get the associated relation - assuming only 1
/*  		foreach($model->relations() as $relationName => $relation)
		{
			// if we have found the relation that uses this attribute which is a foreign key
			if($relation[2] == $fkField)
			{
				$fKModelType = $relation[1];
				$relName = $relationName;
				break;
			}
		}	*/
		
		$fKModelType = static::modelName();

		// set label to passed in label if one passed, otherwise to the tables nice name
		ActiveRecord::$labelOverrides[$fkField] = $label ? $label : $fKModelType::getNiceName();
		
		// if more than 20 rows in the lookup table use autotext
		if($fKModelType::model()->count() > 20)
		{
			static::autoTextWidget($model, $form, $fkField, $htmlOptions, $scopes, $fKModelType/*, $relName*/);
		}
		else
		{
			static::dropDownListWidget($model, $form, $fkField, $htmlOptions, $scopes);
		}
		
	}
	
	static function autoTextWidget($model, $form, $fkField, $htmlOptions, $scopes, $fKModelType/*, $relName*/)
	{
		Yii::app()->controller->widget('WMEJuiAutoCompleteFkField',
			array(
				'model'=>$model,
				'form'=>$form,
				'attribute'=>$fkField,
				'htmlOptions'=>$htmlOptions,
				'scopes'=>$scopes,
				'fKModelType'=>$fKModelType,
//				'relName'=>$relName,
			)
		);
	}
	
	static function dropDownListWidget($model, $form, $fkField, $htmlOptions = array(), $scopes = array())
	{
		$modelName = str_replace('Controller', '', get_called_class());
		$target = new $modelName;
		
		// add a blank value at the top to be converted to null later if allowing nulls
		$listData = isset($model->metadata->columns[$fkField]) && $model->metadata->columns[$fkField]->allowNull
			? array(' '=>'')
			: array();
		$listData += $modelName::getListData($scopes);
		echo $form->dropDownListRow(
			$fkField,
			$listData,
			$htmlOptions + array('name'=>get_class($model)."[$fkField]"),
			$model);
	}

	const accessRead = 'Read';
	const accessWrite = '';
	static function checkAccess($mode, $modelName=null)
	{
		if($mode == self::accessRead || $mode === self::accessWrite)
		{
			return Yii::app()->user->checkAccess(($modelName ? $modelName : static::modelName()) . $mode);
		}
	}

	const reportTypeHtml = 0;
	const reportTypeJavascript = 1;
	public function getReportsMenu($reportType = self::reportTypeHtml, $context = null)
	{
		// if no context model given
		if(!$context)
		{
			// set as this controller
			$context = $this->modelName;
		}
		
		// if we arent going to receive the pk as id at run time via Planning ajaxtree
		if($reportType == self::reportTypeHtml && !empty($_SESSION[$context]['value']))
		{
			// set the primary key
			$pk = $_SESSION[$context]['value'];
		}
		
		$criteria = new CDbCriteria;
		// join
		$criteria->with = array(
			'reportToAuthItems',
		);

		// set the context
		$criteria->condition = 'context = :context OR context IS NULL';
		$criteria->params = array('context' => $context);
		
		foreach(Report::model()->findAll($criteria) as $report)
		{
			// determine if this user has access
			foreach($report->reportToAuthItems as $reportToAuthItem)
			{
				if(Yii::app()->user->checkAccess($reportToAuthItem->AuthItem_name))
				{
					$params['id'] = $report->id;
					$params['context'] = $context;
					if(!empty($pk))
					{
						$params['pk'] = $pk;
					}
					// add menu item
					$items[$report->description] = array(
						'label' => $report->description,
						'url' => Yii::app()->createUrl('Report/show', $params),
						'urlJavascript' => Yii::app()->createUrl('Report/show', array('context'=>$context, 'id' => $report->id))."&pk=\" + id",
					);
				}
			}
		}

		if(!empty($items))
		{
			switch($reportType)
			{
				case self::reportTypeHtml :
					return array(
						'class'=>'bootstrap.widgets.TbMenu',
						'items'=>array(
							array('label'=>'Reports', 'url'=>'#', 'items'=>$items),
						),
					);
				case self::reportTypeJavascript :
					// return report items for context menu in ajax tree
					if(!$itemCount = count($items))
					{
						// if no items then return null
						return 'null';
					}
					$cntr = 0;
					$reportTypeJavascript = '';
					foreach($items as $item)
					{
						// append menu item
						$reportTypeJavascript .= 
							"item$cntr : {
								\"label\"             : \"{$item['label']}\",
								\"action\"            : function (obj) { window.location = \"{$item['urlJavascript']}}
							}".($itemCount > ++$cntr ? ',' : '');
					}
					// return the items formatted for jquery context drop down
					return "{ reports : { 'label' : 'Reports', 'submenu' : {
								{$reportTypeJavascript}
							}}}";
				default :
					throwException();
			}
		}
		
		return $reportType == self::reportTypeHtml ? null : 'null';
	}

	protected function exportButton()
	{
		if(Yii::app()->params['showDownloadButton'])
		{
			echo ' ';
			$this->widget('bootstrap.widgets.TbButton', array(
				'label'=>'Download Excel',
				'url'=>$this->createUrl("{$this->modelName}/admin", array('action'=>'download')),
				'type'=>'primary',
				'size'=>'small', // '', 'large', 'small' or 'mini'
			));
		}
	}

	protected function newButton()
	{
		echo ' ';
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'New',
			'url'=>'#myModal',
			'type'=>'primary',
			'size'=>'small', // '', 'large', 'small' or 'mini'
			'htmlOptions'=>array(
				'data-toggle'=>'modal',
				'onclick' => '$(\'[id^=myModal] input:not([class="hasDatepicker"]):visible:enabled:first, [id^=myModal] textarea:first\').focus();',
			),
		)); 
	}

	protected function navbar()
	{
		$this->widget('bootstrap.widgets.TbNavbar', array(
			'fixed'=>false,
			'brand'=>Yii::app()->name,
			'brandUrl'=>'#',
			'collapse'=>true, // requires bootstrap-responsive.css
			'items'=>array(
				$this->reportsMenu,
				//$this->operations,
				array(
					'class'=>'bootstrap.widgets.TbMenu',
					'items'=>array(
						Yii::app()->user->checkAccess('system admin')
							? array('label'=>'Database', 'url'=>Yii::app()->request->hostInfo.'/phpmyadmin')
							: array(),
					),
				),
				array(
					'class'=>'bootstrap.widgets.TbMenu',
					'htmlOptions'=>array('class'=>'pull-right'),
					'items'=>array(
						array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
						array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
					),
				),
			),
		));
	}

}
?>