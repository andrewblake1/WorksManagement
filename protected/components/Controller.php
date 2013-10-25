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
	public $layout = '//layouts/column1';

	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array();

	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array();

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

	/*
	 * @var array the trail
	 */
	static $trail = NULL;

	/**
	 * @var array the tab menu itemse
	 */
	public static $tabs = array();
	/**
	 * @var bool whether to show the new button in the admin  
	  protected $_adminShowNew = false;
	 */

	/**
	 * @var string the name of the admin view
	 */
	protected $_adminView = '/admin';
	public static $nav = array();

	static function modelName()
	{
		return str_replace('Controller', '', get_called_class());
	}

	public function __construct($id, $module = null)
	{
		$this->modelName = static::modelName();

		// clear the labelOverrides that may have been used in previous view
		ActiveRecord::$labelOverrides = array();

		// set up the buttons for use in admin view
		$this->buttons = array(
			'class' => 'CButtonColumn',
			'template' => '{update}{delete}',
			'buttons' => array(
				'update' => array(
					'imageUrl' => Yii::app()->getAssetManager()->publish(
						Yii::getPathOfAlias('zii.widgets.assets')) . '/gridview/view.png',
				),
			),
		);

		Yii::app()->clientScript->scriptMap = array(
			// TODO: remove this once the bug is fixed in future release of yiibooster
			/* NB: currently version 1.9 of jqueryUI introduces tootip which conflicts with bootstrap.
			 * new version of bootstrap or yiibooster will resolve it - several people working on it
			 * There is a jquery-ui solution evidently using $.widget.bridge from https://github.com/twitter/bootstrap/issues/6303
			 */
			'jquery-ui.min.js' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js',
		);

		// These eaiest here in order to save binding elements after ajax (not bound in doc ready)
		$cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		// at this stage this needed as category controller returnform blocks reload of jqueryui in ajax via scriptMap but
		// need to load jquery ui ahead of bootstrap for radio buttons in bootstrap - otherwise jquery ui conflicts, hence put
		// into package (control the url) in application component - corescripts rendered before script files
		$cs->registerCoreScript('jquery.ui');
		$cs->registerScriptFile('jquery-ui.min.js');

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
				'actions' => array(
					'admin',
					'view',
					'download',
					'returnForm',
//					'autocomplete',
//					'dependantList',
				),
				'roles' => array($this->modelName . 'Read'),
			),
// TODO: this is potential security risk - allowing any authenticated user effectively
// access to all data via these actions - hence need to protect within these actions inside the controller
// i.e. perhaps check that ?? - simple fix for now! See issue 401
			array('allow',
				'actions' => array(
					'autocomplete',
					'dependantList',
				),
				'users' => array('@'),
			),
			array('allow',
				'actions' => array(
					'create',
					'delete',
					'update',
					'batchDelete'
				),
				'roles' => array($this->modelName),
			),
			array('deny', // deny all users
				'users' => array('*'),
			),
		);
	}

	public function actionReturnForm()
	{
		$modelName = $this->modelName;
		
		//don't reload these scripts or they will mess up the page - these allowed in NLSClientScript for ajaxTree - must have some doc load stuff or something
		$cs=Yii::app()->clientScript;
		$cs->scriptMap = array_merge(array(
			'bootstrap.js'=>false,
			'jquery.js'=>false,
			'jquery.yiiactiveform.js'=>false,
			'bootstrap.bootbox.min.js'=>false,
			'bootstrap.notify.js'=>false,
			'jquery-ui.min.js'=>false,
		), $cs->scriptMap);

		// update
		if(isset($_GET['id']))
		{
			$model = $this->loadModel($_GET['id']);
			$action = $this->createUrl('Update', array('id'=>$_GET['id']));
		}
		// create
		else
		{
			$model = new $modelName;
			$action = NULL;
		}

		$model->attributes = $_GET[$modelName];

		$this->renderPartial('_form', array('model' => $model, 'action'=>$action), false, true);
	}

	static function getTrail()
	{
		return self::$trail ? self::$trail : Yii::app()->params['trail'];
	}

	// data provider for EJuiAutoCompleteFkField
	public function actionAutocomplete()
	{
		// if something has been entered
		if(isset($_GET['term']))
		{
			$modelName = $this->modelName;
			$model = $modelName::model();
			$criteria = $model->searchCriteria;
			$criteria->condition = '';
			$displayAttr = $modelName::getDisplayAttr();

			// create the search term
			$concat = "CONCAT_WS(' ', " . implode(', ', $displayAttr) . ")";
			$cntr = 0;
			$criteria->params = array();
			foreach($terms = explode(' ', $_GET['term']) as $term)
			{
				$term = trim($term);
				$paramName = ":param$cntr";
				$criteria->condition .= ($criteria->condition ? " AND " : '') . "$concat LIKE $paramName COLLATE utf8_general_ci";
				$criteria->params[$paramName] = "%$term%";
				$cntr++;
			}

			// limit the results
			$criteria->limit = Yii::app()->params->listMax;
			// get a display string
			foreach($displayAttr as &$attr)
			{
				$order[] = "$attr ASC";
				$attr = '$p->' . $attr;
			}
			// NB: here replaceing possible alias inserted if using ActiveRecord::getDisplayAttr()
			$display = str_replace('t.', '', implode(Yii::app()->params['delimiter']['display'], $displayAttr));
			// add scopes
			$criteria->scopes = empty($_GET['scopes']) ? null : $_GET['scopes'];
			// order
			$criteria->order = implode(', ', $order);

			// get models
			$fKModels = $model->findAll($criteria);

			// if some models founds
			if(!empty($fKModels))
			{
				$out = array();
				$primaryKey = $model->tableSchema->primaryKey;
				$toolTipAttribute = $model->toolTipAttribute;
				foreach($fKModels as $p)
				{
					eval("\$value=\"$display\";");
					$out[] = array(
						// expression to give the string for the autoComplete drop-down
						'label' => $value,
						'value' => $value,
						// return value from autocomplete
						'id' => $p->$primaryKey,
					) + ($toolTipAttribute ? array('title' => $p->$toolTipAttribute) : array());
				}
				echo CJSON::encode($out);
				Yii::app()->end();
			}
		}
	}

	public function behaviors()
	{
		return array(
			'eexcelview' => array(
				'class' => 'ext.eexcelview.EExcelBehavior',
			),
		);
	}

	/*
	 * Adjust the tabs array by only allowing the specified tabs at the specified levels - skips the first tab which is update - only admin views
	 * parameters are exclusions
	 */

	public function trimTabs($topRow, $bottomRow = array(), $middleRows = array())
	{
		// loop thru tabs layers
		for($numLayers = sizeof($this->tabs), $cntr = 1; $cntr <= $numLayers; $cntr++)
		{
			if($cntr == 1)
			{
				$row = $topRow;
			} elseif($cntr == $numLayers)
			{
				$row = $bottomRow;
			} else
			{
				$row = $middleRows;
			}

			// loop thru the individual tabs
			foreach(self::$tabs[$cntr - 1] as $key => $value)
			{
				// if the label is in the exclusion array
				if(in_array($value['label'], $row))
				{
					// remove it from the tabs
					unset(self::$tabs[$cntr - 1][$key]);
				}
			}
		}
	}

	public function getTabs()
	{
		return static::$tabs;
	}

	/**
	 * _tabs property setter. Utilises trail to set tab options.
	 * @param $model if set uses next level down in trail array from the models level if false uses the models level
	 * to extract tab options. Admin and create use models level, update uses next level unless
	 * the current model is a leaf rather than branch (no further branching) in which case popup form will be used to update
	 */
	public function setTabs($model, &$tabs = NULL)
	{
		if($tabs === NULL)
		{
			$tabs = &static::$tabs;
		}

		$level = sizeof($tabs);

		if($model)
		{
			$thisModelName = get_class($model);
		} else
		{
			$thisModelName = $this->modelName;
		}

		// multidimensional array search returns from start of search array to target
		// need to get the next level items from that point only
		// step thru the trail to our target
		$items = self::getTrail();
		// if we should return this level NB: this is empty deliberately to keep condition the same as below
		if(!$model && (isset($items[$thisModelName])))
		{
			
		} else
		{
			$trail = Yii::app()->functions->multidimensional_arraySearch(self::getTrail(), $thisModelName);
			// get tree of items at or below the desired level
			foreach($trail as $key => &$value)
			{
				// if we should return this level
				if(!$model && (isset($items[$thisModelName]) || in_array($thisModelName, $items)))
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
				if($model)
				{ // true for create and update
					// still want to show this model in tabs
					array_unshift($items, $thisModelName);
				}
				// otherwise
				else
				{ // admin
					if(sizeof($trail) > 1)
					{
						// want to show the parent model first in tabs so add to top of items
						array_unshift($items, $trail[sizeof($trail) - 2]);
					}
				}
			} else
			{
				$items = array($thisModelName);
			}
		}

		if($model)
			$t = $model->attributes;
		// if there are items
		if(is_array($items))
		{
			$index = 0;

			foreach($items as $key => &$value)
			{
				// get the model name of this item
				$modelName = is_array($value) ? $key : $value;

				// check access
				if(!static::checkAccess(self::accessRead, $modelName))
				{
					continue;
				}

				// if first item in tabs
				if(!$index)
				{
					// set whether action is update or view
					$action = static::checkAccess(self::accessWrite, $modelName) ? 'update' : 'view';

					// store this (first tabs model name)
					$firstTabModelName = $modelName;
					$firstTabPrimaryKeyName = $firstTabModelName::primaryKeyName();
					if($model)
					{
						$keyValue = $model->$firstTabPrimaryKeyName;
					} else
					{
						$keyValue = static::getUpdateId($firstTabModelName) !== NULL ? static::getUpdateId($firstTabModelName) : NULL;
					}

					// if nextlevel is true then action should always be update, but also should be update if current model is this model
					// and not next level
					// create controler/action
					if($keyValue && (!$model || ($firstTabModelName == $thisModelName)))
					{
						$urlParams = array("$modelName/$action", $firstTabPrimaryKeyName => $keyValue);
						// add any existing get parameters - but only valid ones
						$urlParams = array_merge($urlParams, static::getValidGetParams($modelName));
						$tabs[$level][$index]['label'] = $modelName::getNiceName($keyValue);
						$tabs[$level][$index]['url'] = $urlParams;
						// assume active unless otherwise indicated
						// if this item matches the main model
						if($modelName == $thisModelName)
						{
							// make this the active tab
							$tabs[$level][$index]['active'] = true;
						}
						$index++;
						continue;
					}
				}

				// get name of foreign key to parent
				$parentForeignKeyName = $modelName::getParentForeignKey($firstTabModelName);

				// dont add empty tabs if no write access
				if(!static::checkAccess(self::accessWrite, $modelName) && $parentForeignKeyName && !$modelName::model()->countByAttributes(array($parentForeignKeyName => $keyValue)))
				{
					continue;
				}

				// if this item matches the main model
				if($modelName == $thisModelName)
				{
					// make this the active tab
					$tabs[$level][$index]['active'] = true;
				}

				// add relevant url parameters i.e. foreign key to first tab model
				$urlParams = ($keyValue === null) ? array() : array($parentForeignKeyName => $keyValue);
				// add any existing get parameters
				$urlParams = array_merge($urlParams, static::getValidGetParams($modelName));

				$tabs[$level][$index]['label'] = $modelName::getNiceNamePlural();
				$tabs[$level][$index]['url'] = array("$modelName/admin") + $urlParams;
				$index++;
			}
		}
	}

	static function getValidGetParams($modelName = NULL, $inParams = NULL)
	{
		$get = array();

		if(!$modelName)
		{
			$modelName = static::modelName();
		}
		
		if($inParams === NULL)
		{
			$inParams = $_GET;
		}

		$primaryKeyName = $modelName::primaryKeyName();

		foreach($inParams as $key => $value)
		{
			if($modelName::model()->hasAttribute($key))
			{
				// ignore primary key field
				if($key != $primaryKeyName)
				{
					$get[$key] = $value;
				}
			}
		}

		return $get;
	}

	public function addTab($label, $modelName, $action, $params, &$tabs, $active = FALSE, $model = NULL)
	{
		$writeAccess = $model ? $model->checkAccess(self::accessWrite) : static::checkAccess(self::accessWrite, $modelName);

		// dont add empty admin tabs if no write access
		if($action == 'admin' && !$writeAccess && !$modelName::model()->countByAttributes(array_intersect_key($params, array_flip($modelName::model()->attributeNames()))))
		{
			return;
		}

		if($action == 'update')
		{
			$action = $writeAccess ? 'update' : 'view';
		}

		$index = sizeof($tabs);
		$tabs[$index]['label'] = $label;
		$tabs[$index]['url'] = $this->createUrl("$modelName/$action", $params);
		// NB: last one active taken care of somewhere by matching url - unless blocked here
		if($active)
		{
			$tabs[$index]['active'] = $active;
		}
	}

	protected function restoreAdminSettings(&$modelName, &$container = NULL)
	{
		if($container === NULL)
		{
			$container = &$_SESSION['admin'][$modelName];
		}

		// restore pagination
		if(isset($container['page']))
		{
			$_GET["{$modelName}_page"] = $container['page'];
		}
		// restore sort
		if(isset($container['sort']))
		{
			$_GET["{$modelName}_sort"] = $container['sort'];
		}
		// restore filters
		if(isset($container['filter']))
		{
			if(isset($_GET["{$modelName}"]))
			{
				$_GET["{$modelName}"] += $container['filter'];
			} else
			{
				$_GET["{$modelName}"] = $container['filter'];
			}
		}
	}

	protected function storeAdminSettings(&$modelName, &$container = NULL)
	{
		if($container === NULL)
		{
			$container = &$_SESSION['admin'][$modelName];
		}

		// if some filters
		if(isset($_GET[$modelName]))
		{
			// store filters
			$container['filter'] = $_GET[$modelName];
		} elseif(isset($container['filter']))
		{
			// clear filters
			unset($container['filter']);
		}

		// if pagination
		if(isset($_GET["{$modelName}_page"]))
		{
			// store pagination
			$container['page'] = $_GET["{$modelName}_page"];
		} elseif(isset($container['page']))
		{
			// clear filters
			unset($container['page']);
		}

		// if sorting
		if(isset($_GET["{$modelName}_sort"]))
		{
			// store sorting
			$container['sort'] = $_GET["{$modelName}_sort"];
		} elseif(isset($container['sort']))
		{
			// clear sorting
			unset($container['sort']);
		}
	}

	public function actionAdmin($exportColumns = array())
	{
		$modelName = $this->modelName;
		$model = new $modelName('search');
		$model->unsetAttributes();  // clear any default values
		// clear the primary key set by update
		static::setUpdateId(NULL, $modelName);

		if(isset($_GET['ajax']))
		{
			// get around bug where ajaxurl needs to be set to current url in gridview because of params but this prevents
			// gridview update maintaining paging etc after as the params are no longer appended to the update because of a javascript bug
			// in parsing the url with params. Was recording somewhere in the yii forum by someone else
			// This means we have to extend the pager in order to add a paging link for page 1 or that won't work
			// if paging or filtering or sorting
			if(isset($_GET["{$modelName}_sort"]) || isset($_GET["{$modelName}_page"]) || isset($_GET[$modelName]))
			{
				$this->storeAdminSettings($modelName);
			}
		}
		// otherwise non ajax call
		elseif(isset($_GET))
		{
			// store admin url paramters
			static::setAdminParams($_GET, $modelName);
		}

		$this->restoreAdminSettings($modelName);

		$attributes = array();
		if(!empty($_GET))
		{
			$attributes += $_GET;
		}
		if(!empty($_GET[$modelName]))
		{
			$attributes += $_GET[$modelName];
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
			$this->toExcel($model->search(false), $model->exportColumns, null, array());
		}

// TODO excel5 has issue on isys server likely caused by part of phpexcel wanting access to /tmp but denied		
// TODO excel2007 best format however mixed results getting succesfull creations with this = varies across servers likely php_zip issue	thnk
// it works on windows machine however not mac nor linux for me so far.
		// set heading
//		if (!$this->heading) {
		$this->heading = $modelName::getNiceNamePlural();
//		}
		// set breadcrumbs
		$this->breadcrumbs = static::getBreadCrumbTrail();

		// render the view
		$this->adminRender($model);
	}

	protected function adminRender($model)
	{
		if(!isset($_GET['ajax']))
		{
			// set up tab menu if required - using setter
			$this->tabs = false;
		}

		$this->render(lcfirst($this->_adminView), array(
			'model' => $model,
		));
	}

	public function getButtons($model)
	{
		return array(
			'class' => 'WMTbButtonColumn',
			'buttons' => array(
				'delete' => array(
					'visible' => 'Yii::app()->user->checkAccess("' . $this->modelName . '", array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/delete", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
				'update' => array(
					'visible' => 'Yii::app()->user->checkAccess("' . $this->modelName . '", array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/update", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
				'view' => array(
					'visible' => '
						!Yii::app()->user->checkAccess("' . $this->modelName . '", array("primaryKey"=>$data->primaryKey))
						&& Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/view", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
			),
		);
	}

	static function getParentCrumb($modelName = null)
	{
		if(empty($modelName))
		{
			$modelName = static::modelName();
		}

		$trail = array_reverse(Yii::app()->functions->multidimensional_arraySearch(self::getTrail(), $modelName));

		if(!empty($trail[1]))
		{
			return $trail[1];
		}
	}

	protected static function makeCrumbAdmin($displays, $queryParamters)
	{
		$modelName = static::modelName();
		return array($displays => array("$modelName/admin") + $queryParamters);
	}

	/**
	 * Get the breadcrumb trail for this controller.
	 * return array bread crumb trail for this controller
	 */
	static function getBreadCrumbTrail($lastCrumb = NULL)
	{
		$queryParamters = array();
		$breadcrumbs = array();
		$modelName = static::modelName();

		// if just gone direct to a screen i.e. our memory/history was cleared
		if(static::getAdminParams() === NULL && !$lastCrumb)
		{
			if(static::checkAccess(self::accessRead))
			{
				$breadcrumbs[] = array($modelName::getNiceName());
			}
			return $breadcrumbs;
		}

		// loop thru the trail for this model
		foreach(Yii::app()->functions->multidimensional_arraySearch(self::getTrail(), $modelName) as $crumb)
		{
			// check access
			if(!static::checkAccess(self::accessRead, $crumb))
			{
				continue;
			}

			// add any other valid paramaters from previous breadcrumbs
			$queryParamters = Controller::getValidGetParams($crumb, $queryParamters);

			// foreign key to the parent
			if($parentForeignKey = $modelName::getParentForeignKey($parentCrumb = static::getParentCrumb($crumb)))
			{
				if(static::getAdminParam($parentForeignKey, $modelName) !== NULL)
				{
					$queryParamters[$parentForeignKey] = static::getAdminParam($parentForeignKey, $modelName);
				} elseif(static::getUpdateId($parentCrumb))
				{
					$queryParamters[$parentForeignKey] = static::getUpdateId($parentCrumb);
				}
			}

			$displays = $crumb::getNiceNamePlural();

			// add crumb to admin view
			$crumbController = ucfirst($crumb) . 'Controller';
			$breadcrumbs[] = $crumbController::makeCrumbAdmin($displays, $queryParamters);

			// if this is the last crumb
			if($modelName == $crumb)
			{
				if($lastCrumb == 'Create')
				{
					// add last crumb
					$breadcrumbs[] = array($crumb::getCreateLabel());
				} elseif($lastCrumb == 'Update')
				{
					// add a crumb with just the primary key nice name but no href
					$primaryKey = static::getUpdateId($crumb);
					$breadcrumbs[] = array($crumb::getNiceName($primaryKey));
				}
			}
			// otherwise not last crumb
			else
			{
				// if there is a primary key for this
				if(static::getUpdateId($crumb) !== NULL)
				{
					// add an update crumb to this primary key
					$primaryKey = static::getUpdateId($crumb);
					$breadcrumbs[] = array($crumb::getNiceName($primaryKey) => array("$crumb/" . (static::checkAccess(self::accessWrite, $crumb) ? 'update' : 'view'),
						$crumb::model()->tableSchema->primaryKey => $primaryKey,
					) + $queryParamters);
				}
			}
		}

		/**
		 * Add in from multi-level tabs
		 */
		if(sizeof(static::$tabs) > 1)
		{
			array_pop($breadcrumbs);

			$numTabRows = sizeof(static::$tabs);
			$rowCount = 0;
			// easiest to just get from active tabs
			foreach(static::$tabs as $index => &$tabsRow)
			{
				$rowCount++;
				// loop thru tabs in row
				$tabCount = 0;
				foreach($tabsRow as &$tab)
				{
					if(!empty($tab['active']) && $tab['active'] == TRUE)
					{
						// add link to the first item in the row -- if not first row
						if($rowCount > 1)
						{
							$url = is_array($tabsRow[0]['url']) ? $tabsRow[0]['url'] : array($tabsRow[0]['url']);
							$breadcrumbs[] = array($tabsRow[0]['label'] => $url);
						}

						if(($rowCount != $numTabRows) || $tabCount)
						{
							$breadcrumbs[] = array($tab['label'] => $tab['url']);
						}
					}
					$tabCount++;
				}
			}

			// ensure the last one is just text
			$count = sizeof($breadcrumbs);
			$breadcrumbs[$count - 1] = array(key($breadcrumbs[$count - 1]));
		}


		return $breadcrumbs;
	}

	/*
	 * Look at breadcrumb trail and if this is a leaf node then redirect to admin, otherwise it is likely the user
	 * will want to edit some of the branches or leaves below hence redirect to update
	 */

	protected function createRedirect($model, $params = array())
	{
		$items = Yii::app()->params['trail'];
		$trail = Yii::app()->functions->multidimensional_arraySearch(Yii::app()->params['trail'], $this->modelName);
		// get tree of items at or below the desired level
		foreach($trail as $key => &$value)
		{
			// if there are items below
			$items = isset($items[$value]) ? $items[$value] : array();
		}

		// if there are some child items
		if(sizeof($items))
		{
			// go to update view
			$this->redirect(array_merge(array('update', $model->tableSchema->primaryKey => $model->getPrimaryKey()), $params));
		} else
		{
			// go to admin view
			$model->assertFromParent();
			$this->adminRedirect($model, false, $params);
		}
	}

	/*
	 * to be overidden if not wanting to redirect to admin
	 */

	protected function updateRedirect($model)
	{

		if(!empty($_POST['controller']))
		{
			$modelName = $_POST['controller'];
		} else
		{
			$modelName = get_class($model);
		}

		// need to be careful not to filter by the update id
		$primaryKeyName = $modelName::model()->tableSchema->primaryKey;
		if(isset($_GET[$primaryKeyName]))
		{
			unset($_GET[$primaryKeyName]);
		}

		$this->adminRedirect($model);
	}

	public static function staticAdminRedirect($model, $sortByNewest = false, $params = array())
	{

		// if posted a controller then this is where we should return to
		if(!empty($_POST['controller']))
		{
			$modelName = $_POST['controller'];
		} else
		{
			$modelName = get_class($model);
		}

		$params = array_merge(array("$modelName/admin"), ($params + static::getAdminParams($modelName) + $_GET));

		// if we want to sort by the newest record first
		if($sortByNewest)
		{
			$model->adminReset();
			$params["{$modelName}_sort"] = $modelName::model()->tableSchema->primaryKey . '.desc';
		}

		Yii::app()->controller->redirect($params);
	}

	// redirect to admin
	protected function adminRedirect($model, $sortByNewest = false, $params = array())
	{
		static::staticAdminRedirect($model, $sortByNewest, $params);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($modal_id = 'myModal', &$model = null)
	{
		if($model === null)
		{
			$model = new $this->modelName;
		}
		$models = array();

		$model->attributes = $_GET;
$t = $model->attributes;

		// $validating will be set to true if ajax validating and passed so-far but still need to try, catch db errors before actual submit
		$validating = $this->performAjaxValidation($model);
// TODO: this is untested without javascript
		if(isset($_POST[$this->modelName]))
		{
			$model->attributes = $_POST[$this->modelName];
			// ensure Controller::$nav is set
			$model->assertFromParent();
			// start a transaction
			$transaction = Yii::app()->db->beginTransaction();

			// attempt save
			$saved = $model->createSave($models);

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
					$result = array();
					if(!is_array($models))
					{
						$models = array($model);
					}
					foreach($models as $m)
					{
						foreach($m->getErrors() as $attribute => $errors)
						{
							$result[$m->getHtmlId($attribute)] = $errors;
						}
					}
					// return the json encoded data to the client
					echo $t = function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
					Yii::app()->end();
				}

				$model->isNewRecord = TRUE;
			}
		} elseif(isset($_GET[$this->modelName]))
		{
			// set any url based paramters
			$model->attributes = $_GET[$this->modelName];
			// ensure Controller::$nav is set
			$model->assertFromParent();
		}

// TODO: check this code might be obsolete		
		// if just failed to save after ajax validation ok'd it - maybe an invalid file upload which can't use ajax validation
		if(isset($saved) && !$saved)
		{
			// get errors
			$message = '
				<strong>Sorry, could,\'t save because</strong>
					<ul>';
			foreach($models as $m)
			{
				foreach($m->getErrors() as $attribute => $errors)
				{
					foreach($errors as $error)
					{
						$message.="<li>$error</li>";
					}
				}
			}
			$message .= '</ul>';
// TODO: use jquery show to show the create form on reentry and show the validation error message therefore pass the error message to createrender
// instead of flash message which is just a quick temporary solution
			Yii::app()->user->setFlash('error', $message);
			// redirect back to this view - most likely admin but pass paramter to let know about failed validation
			$this->createRedirect($model);
		}

		$this->createRender($model, $models, $modal_id);
	}

	protected function createRender($model, $models, $modal_id)
	{
		// set tabs
//		$this->tabs = $model;

		$this->widget('CreateViewWidget', array(
			'model' => $model,
			'models' => $models,
			'modal_id' => $modal_id,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id, $model = null)
	{
		$modelName = $this->modelName;

		if($model === null)
		{
			$model = $this->loadModel($id);
		}
		$models = array();

		// add primary key into global so it can be retrieved for future use in breadcrumbs
		static::setUpdateId($id, $modelName);

		// ensure that where possible a pk has been passed from parent and get that fk name if possible
		$parent_fk = $model->assertFromParent();

		// $validating will be set to true if ajax validating and passed so-far but still need to try, catch db errors before actual submit
		$validating = $this->performAjaxValidation($model);
// TODO: this is untested without javascript

		if(isset($_POST[$modelName]))
		{
			$model->attributes = $_POST[$modelName];
			// start a transaction
			$transaction = Yii::app()->db->beginTransaction();

			// attempt save
			$saved = $model->updateSave($models);

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
					$result = array();
					if(!is_array($models))
					{
						$models = array($model);
					}
					foreach($models as $m)
					{
						foreach($m->getErrors() as $attribute => $errors)
						{
							$result[$m->getHtmlId($attribute)] = $errors;
						}
					}
					// return the json encoded data to the client
					echo $t = function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
					Yii::app()->end();
				}

				$model->isNewRecord = TRUE;
			}
		}

		// otherwise this is just a get and could be passing paramters
		if(!empty($_GET[$modelName]))
		{
			$model->attributes = $_GET[$modelName];
		}

		// set heading
		$this->heading = $modelName::getNiceNameShort($id);

		// set breadcrumbs
		$this->breadcrumbs = static::getBreadCrumbTrail('Update');

		// set tabs
		$this->tabs = $model;

		// render the widget
		$this->widget('UpdateViewWidget', array(
			'model' => $model,
			'models' => $models,
			'parent_fk' => $parent_fk,
		));
	}

	/**
	 * 
	 * @param type $niceName	text to look for
	 * @param type $first
	 * @param type $last
	 * @param type $middle 
	 */
	public function setActiveTabs($first = NULL, $last = NULL, $middle = NULL)
	{
		$success = FALSE;

		$sizeofTabs = sizeof(static::$tabs);

		if($first)
		{
			$this->setActiveTab(static::$tabs[0], $first);
		}
		if($last)
		{
			$this->setActiveTab(static::$tabs[$sizeofTabs - 1], $last);
		}
		if($middle)
		{
			for($cntr = 1; $cntr < ($sizeofTabs - 1); $cntr++)
			{
				$this->setActiveTab(static::$tabs[$cntr], $middle);
			}
		}
	}

	public function setActiveTab(&$tabsRow, $niceName = NULL)
	{
		// no value passed to look for
		if($niceName === NULL)
		{
			$modelName = $this->modelName;
			$niceName = $modelName::getNiceName();
		}

		// loop thru tabs
		foreach($tabsRow as &$tab)
		{
			// if we have a match
			if($tab['label'] == $niceName)
			{
				$tab['active'] = TRUE;
			}
			// othersise make inactive
			else
			{
				$tab['active'] = FALSE;
			}
		}
	}

	/**
	 * Views a particular model.
	 * @param integer $id the ID of the model to be viewed
	 */
// TODO: the guts of this is duplicated in actionUpdate
	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$primaryKeyName = $model->tableSchema->primaryKey;

		// otherwise this is just a get and could be passing paramters
		$model->$primaryKeyName = $id;

		// set heading
		$modelName = $this->modelName;
		$this->heading = $modelName::getNiceNameShort($id);

		// add primary key into global so it can be retrieved for future use in breadcrumbs
		static::setUpdateId($id, $modelName);
		$model->assertFromParent();
//$t = Controller::$nav;		
		// set breadcrumbs
		$this->breadcrumbs = static::getBreadCrumbTrail('Update');

		$t = $model->attributes;
		// set up tab menu if required - using setter
		$this->tabs = $model;

		$this->widget('UpdateViewWidget', array(
			'model' => $model,
//			'models'=>$models,
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
				$model->delete();
			} catch(CDbException $e)
			{
				$message = $e->getMessage();
				if(!isset($_GET['ajax']))
				{
					Yii::app()->user->setFlash('error', "<strong>System admin error!</strong>
						$message");
				} else
				{
					echo "
						<div class='alert alert-block alert-error fade in'>
							<a class='close' data-dismiss='alert'>×</a>
							<strong>System admin error!</strong>
							$message
						</div>
					";
				}
			}

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
			{
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin', $this->modelName => static::getAdminParams($this->modelName)));
			}
		} else
		{
			throw new CHttpException(400, 'Invalid request.');
		}
	}

	// from http://www.yiiframework.com/forum/index.php/topic/37941-how-to-use-bulk-action-in-yiibooster/
	public function actionBatchDelete()
	{
		//  print_r($_POST);
		$request = Yii::app()->getRequest();

		if($request->getIsPostRequest())
		{
			if(isset($_POST['ids']))
			{
				$ids = $_POST['ids'];
			}

			$successCount = $failureCount = 0;
			foreach($ids as $id)
			{
				$model = $this->loadModel($id);
				($model->delete() == true) ? $successCount++ : $failureCount++;
			}

			echo CJSON::encode(array(
				'status' => 'success',
				'msg' => $failureCount ? "
						<div class='alert alert-block alert-error fade in'>
							<a class='close' data-dismiss='alert'>×</a>
							<strong>Oops!</strong>
							Errors occurred. $successCount deleted but
							$failureCount failed to be deleted - contact system admin, this is a bug.
						</div>" : '',
			));

			die();
		} else
		{
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id, $model = null)
	{
		$modelName = $this->modelName;
		if($model === null)
		{
			$model = $modelName::model()->findByPk($id);
			if($model === null)
			{
				throw new CHttpException(404, 'The requested page does not exist.');
			}
		}

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		$t = $model->attributes;
		$validating = false;
		if(isset($_POST['ajax']) && $_POST['ajax'] === $this->modelName . '-form')
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
		$fKModelType = static::modelName();

		// set label to passed in label if one passed, otherwise to the tables nice name
		ActiveRecord::$labelOverrides[$fkField] = $label ? $label : $fKModelType::getNiceName();

		$criteria = new CDbCriteria();
		$criteria->scopes = $scopes;
		// if more than x rows in the lookup table use autotext
		if($fKModelType::model()->count($criteria) > Yii::app()->params->listMax)
		{
			static::autoCompleteFKFieldRow($model, $form, $fkField, $htmlOptions, $scopes, $fKModelType);
		} else
		{
			static::dropDownListFKfieldRow($model, $form, $fkField, $htmlOptions, $scopes);
		}
	}

	static function autoCompleteFKFieldRow($model, $form, $fkField, $htmlOptions, $scopes, $fKModelType)
	{
		Yii::app()->controller->widget('WMEJuiAutoCompleteFkField', array(
			'model' => $model,
			'form' => $form,
			'attribute' => $fkField,
			'htmlOptions' => $htmlOptions + array('class' => 'span5'),
			'scopes' => $scopes,
			'fKModelType' => $fKModelType,
			'methodChain'=>'.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a data-original-title=\'" + item.title +  "\'>" + item.label +  "</a>" )
					.appendTo( ul );
			};'
		));
		
	}

	static function dropDownListFKfieldRow($model, $form, $fkField, $htmlOptions = array(), $scopes = array())
	{
		$modelName = str_replace('Controller', '', get_called_class());
		$target = new $modelName;

		// add a blank value at the top to be converted to null later if allowing nulls
		$allowNull = TRUE;
		foreach($model->rules() as $rule)
		{
			if($rule[1] == 'required' && in_array($fkField, explode(',', preg_replace('/\s/', '', $rule[0]))))
			{
				$allowNull = FALSE;
			}
		}

		// javascript bubbling will remove please select item after selection or allow blank otherwise
		$htmlOptions['empty'] = $allowNull ? '' : 'Please select';
		
		$id = CHtml::activeId($model, $fkField);
		$options = array();
		$listData = $modelName::getListData($scopes, $options);

		// allow for possible tooltips
		if(sizeof($options))
		{
			$htmlOptions['options'] = $options + (isset($htmlOptions['options']) ? $htmlOptions['options'] : array());
			Yii::app()->clientScript->registerScript("tooltip_$id", '
				$("#' . $id. '").on("mouseover", function(e) {
					$("#' . $id. '").tooltip("destroy");
					var $e = $(e.target); 
					if ($e.is("option")) {

							$("#' . $id. '").tooltip({
								trigger: "manual",
								placement: "top",
								title: $e.attr("data-original-title"),
							}).tooltip("show");
					} 
				});
				$("#' . $id. '").on("mouseleave", function(e) {
					$("#' . $id. '").tooltip("destroy");
				});'
			);
		}
		
		echo $form->dropDownListRow(
			$fkField, $listData, $htmlOptions + array('name' => get_class($model) . "[$fkField]"), $model);
	}

	const accessRead = 'Read';
	const accessWrite = '';

	static function checkAccess($mode, $modelName = null)
	{
		// always allow dashboard
		if($modelName == 'Dashboard')
		{
			return TRUE;
		}
		// otherwise if child dashboard then replace the Dashboard portion out to check the suffix part of the name
		$modelName = str_replace('Dashboard', '', $modelName, $count);

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
		if($reportType == self::reportTypeHtml && (static::getUpdateId($context) !== NULL))
		{
			// set the primary key
			$pk = static::getUpdateId($context);
		}

		$criteria = new CDbCriteria;

		// with
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
				if(Yii::app()->user->checkAccess($reportToAuthItem->auth_item_name))
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
						'urlJavascript' => Yii::app()->createUrl('Report/show', array('context' => $context, 'id' => $report->id)) . "&pk=\" + id",
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
						'class' => 'bootstrap.widgets.TbMenu',
						'items' => array(
							array('label' => 'Reports', 'url' => '#', 'items' => $items),
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
							}" . ($itemCount > ++$cntr ? ',' : '');
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

	protected function navbar()
	{
		$this->widget('bootstrap.widgets.TbNavbar', array(
			'fixed' => false,
			'htmlOptions' => array('class' => 'navbar-inverse'),
			'brand' => Yii::app()->name,
			'brandUrl' => '#',
			'collapse' => true, // requires bootstrap-responsive.css
			'items' => array(
				$this->reportsMenu,
				//$this->operations,
				array(
					'class' => 'bootstrap.widgets.TbMenu',
					'items' => array(
						Yii::app()->user->checkAccess('system admin') ? array('label' => 'Database', 'url' => Yii::app()->request->hostInfo . '/phpmyadmin') : array(),
					),
				),
				array(
					'class' => 'bootstrap.widgets.TbMenu',
					'htmlOptions' => array('class' => 'pull-right'),
					'items' => array(
		//				array('label' => 'Login', 'url' => array('/site/login'), 'visible' => Yii::app()->user->isGuest),
						array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest),
					),
				),
			),
		));
	}

	static function dependantListWidgetRow($model, $form, $fkField, $dependantOnModelName, $dependantOnAttribute, $htmlOptions, $scopes = array(), $label = null, $modelScopes = array(), $betweenHtml = null)
	{
		$modelName = get_class($model);
		$listModelName = static::modelName();

		$inHtmlOptions = $htmlOptions;	// without the id that is about to calculated
		CHtml::resolveNameID($model, $attribute = $fkField, $htmlOptions);

		$source = Yii::app()->createUrl("$listModelName/autocomplete") . "?model=$modelName&attribute=$fkField&scopes%5Bscope$dependantOnModelName%5D%5B0%5D=";

		$dependantOnControllerName = $dependantOnModelName . 'Controller';

		$dependantOnControllerName::listWidgetRow($model, $form, $dependantOnAttribute, array(
			'ajax' => array(
				'type' => 'POST',
				'url' => Yii::app()->createUrl("$listModelName/DependantList", array('fkField' => $fkField, 'dependantOnModelName' => $dependantOnModelName, 'dependantOnAttribute' => $dependantOnAttribute)),
				'success' => "function(data) {
						if(data)
						{
							data = $(data);

							// if single item list box then need to hide the listbox as a non editable text box will have been sent also
							if($(data).filter('#{$htmlOptions['id']}_dummy').length != 0)
							{
								data = data.filter('select').hide().end();
							}
							
							$('[for=\"{$htmlOptions['id']}\"]').remove();
							$('#{$htmlOptions['id']}_save').remove();
							$('#{$htmlOptions['id']}_em_').remove();
							$('#{$htmlOptions['id']}_lookup').remove();

							// may also need to remove other related disable text box if last item produced one
							// as a result of being a single item select - subbed with non-editing text box
							$('#{$htmlOptions['id']}_dummy').remove();
								
							// do the final substitution
							$('#{$htmlOptions['id']}').replaceWith(data);
							// if this is autotext
							lookup = $('#{$htmlOptions['id']}_lookup');
							if(lookup.length)
							{
								$dependantOnAttribute = $('#{$modelName}_$dependantOnAttribute').val();
								lookup.autocomplete({'minLength':1,'maxHeight':'100','select':function(event, ui){" . "$('#{$htmlOptions['id']}').val(ui.item.id);$('#{$htmlOptions['id']}_save').val(ui.item.value);},'source':'$source' + $dependantOnAttribute});
							}
						}
					}",
			)
			) + $inHtmlOptions, $modelScopes, $label
		);
		
		// append any html in between
		echo $betweenHtml;								
								
		// get the id used from the triggering item
		CHtml::resolveNameID($model, $dependantOnAttribute, $dependsOnHtmlOptions);

		if($model->isNewRecord)
		{
			// trigger the change handler on document load to ensure that the dependency is set correctly to begin with
			Yii::app()->clientScript->registerScript('dropDownListRow' . $htmlOptions['id'], "
				// trigger the change handler
				$('#{$dependsOnHtmlOptions['id']}').trigger('change');
				", CClientScript::POS_READY
			);
		}

		// NB: need to set this here as otherwise in wmfkautocomplete the soure url has standard_id=, in it which gets stripped
		echo "<div id=\"dependant-$fkField\">";
		static::listWidgetRow($model, $form, $fkField, $htmlOptions, $scopes);
		echo '</div>';
		
	}

	public function actionDependantList()
	{
		$modelName = $_POST['controller'];
		ob_start();
		// get the model name of the lookup table from fk field
		$lookupModelName = $this->getFKModelType($modelName::model(), $_GET['fkField']);
		$form = $this->beginWidget('WMTbActiveForm', array('model' => $modelName::model(), 'parent_fk' => $_GET['fkField']));
		ob_end_clean();
		$scopeValue = isset($_POST[$modelName][$_GET['dependantOnAttribute']])
			? $_POST[$modelName][$_GET['dependantOnAttribute']]
			: NULL;
		self::listWidgetRow($modelName::model(), $form, $_GET['fkField'], array(), array('scope' . $_GET['dependantOnModelName'] => array($scopeValue)));
	}

	// recursive to find our way thru relations to target fk model
	private function getFKModelType(&$model, $fkField)
	{
		// get the associated relation - assuming only 1
		foreach($model->relations() as $relationName => $relation)
		{
			// if we have found the relation that uses this attribute which is a foreign key
			if($relation[0] == ActiveRecord::BELONGS_TO && $relation[2] == $fkField)
			{
				return $relation[1];
			}
		}
	}

	static function getAdminParam($paramName, $modelName = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		if(isset(Controller::$nav['admin'][$modelName][$paramName]))
		{
			return Controller::$nav['admin'][$modelName][$paramName];
		}
	}

	static function getAdminParams($modelName = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		if(isset(Controller::$nav['admin'][$modelName]))
		{
			return Controller::$nav['admin'][$modelName];
		}

		return array();
	}

	static function setAdminParam($paramName, $value, $modelName = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		Controller::$nav['admin'][$modelName][$paramName] = $value;
	}

	static function setAdminParams($params, $modelName = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		Controller::$nav['admin'][$modelName] = $params;
	}

	// return last or specified level of update id
	static function getUpdateId($modelName = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		return isset(Controller::$nav['update'][$modelName]) ? Controller::$nav['update'][$modelName] : NULL;
	}

	// return last or specified level of update id
	static function setUpdateId($updateId, $modelName = NULL)
	{
		$modelName = $modelName ? $modelName : static::modelName();

		Controller::$nav['update'][$modelName] = $updateId;
	}

	protected function newButton()
	{
		if(Yii::app()->user->checkAccess($this->modelName))
		{
			echo ' ';
			$this->widget('bootstrap.widgets.TbButton', array(
				'label' => 'New',
				'icon' => 'plus',
				'url' => '#myModal',
				'type' => 'primary',
				'size' => 'small', // '', 'large', 'small' or 'mini'
				'htmlOptions' => array(
					'data-toggle' => 'modal',
// removed until boostrap updated - currently causes issues if tooltip on same form
//					'onclick' => '$(\'#myModal input\').first().focus();',
				),
			));
		}
	}

	public function exportButton()
	{
		if(Yii::app()->params['showDownloadButton'])
		{
			echo ' ';
			$this->widget('bootstrap.widgets.TbButton', array(
				'label' => 'Download Excel',
				'icon' => 'download',
				'url' => $this->createUrl("{$this->modelName}/admin", $_GET + array('action' => 'download')),
				'type' => 'primary',
				'size' => 'small', // '', 'large', 'small' or 'mini'
			));
		}
	}

	public function renderAdminButtons()
	{
		$this->exportButton();
		$this->newButton();
	}

	/*
	 * If can't determine relation automaticatlly (as in has many through) getRelation in getRelation WMEJuiAutoCompleteFkField
	 * then this allows hard coding of return result by controller
	 */

	public function getRelation($model, $attribute)
	{
		
	}

	public function createUrl($route, $params = array(), $ampersand = '&')
	{
		return parent::createUrl($route, array_filter($params), $ampersand);
	}

}

?>