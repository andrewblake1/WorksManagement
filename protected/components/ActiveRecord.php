<?php
abstract class ActiveRecord extends CActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchUser;
	public $naturalKey;
	/**
	 * @var array of labels to override or set at run time
	 */
	static $labelOverrides = array();
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName;
// TODO: these should be in controller and not model
	/**
	 * @var string label for tab and breadcrumbs when creating
	 */
	static $createLabel;
	/**
	 * @var string label on button in create view
	 */
	static $createButtonText;
	/**
	 * @var string label on button in update view
	 */
	static $updateButtonText;
	/**
	 * @var string default attribute to sort by
	 */
	protected $defaultSort = null;
	/*
	 * array of validation rules appended to rules at run time as determined
	 * by the related CustomField
	 */
	public $customValidators = array();
	/**
	 * @var bool whether or not to show soft deleted records or not. Important that related records can
	 * but the actual record hidding within its normaly model, lists to block further selection.
	 */
	public static $showSoftDeletes = FALSE;
	/**
	 * @var bool flag used as addional scenerio indicator when forcing re-read of meta data - needed in tableName()
	 */
	public static $inSearch = false;

	/**
	 * @var string the name of a tooltip field if any to provide tooltip where needed e.g. drop down lists
	 */
	public $toolTipAttribute;

	public static function primaryKeyName()
	{
		return self::model()->tableSchema->primaryKey;
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Client the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model(get_called_class());
	}

	public function findByPk($pk, $condition = '', $params = array()) {
		// need to set a global within this object to block the deleted default scope from being applied
		// when looking for a specific record - which will be a related record hence the whole reason
		// for soft deletes and will cause an expepected null return otherwise
		
// TODO: poor design to not rely on global not great design to rely on a global for this purpose - however working
// within Yii, scopes seemed logical for the soft deletes. Only other way is to deal with soft deletes individually
// which is currently less desirable than the global object level global.
		static::$showSoftDeletes = TRUE;
		
		$return = parent::findByPk($pk, $condition, $params);
	
		static::$showSoftDeletes = FALSE;
		
		return $return;
	}
	
	public function getRelated($name,$refresh=false,$params=array())
	{
		$md=$this->getMetaData();
		if(isset($md->relations[$name]))
		{
			$relation=$md->relations[$name];
			if(!$this->getIsNewRecord() && $relation instanceof CBelongsToRelation)
			{
				static::$showSoftDeletes = TRUE;
			}
		}

		$return = parent::getRelated($name,$refresh,$params);

		static::$showSoftDeletes = FALSE;
		
		return $return;
	}
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_' . Yii::app()->functions->uncamelize(get_class($this));
	}
	
	public function getCreateButtonText()
	{
		return static::$createButtonText ? static::$createButtonText : 'Create';
	}
	
	public function getUpdateButtonText()
	{
		return static::$updateButtonText ? static::$updateButtonText : 'Update';
	}
	
	public static function getCreateLabel()
	{
		return static::$createLabel ? static::$createLabel : 'Create';
	}
	
	/*
	 * return all attributes - table properties and other object variables
	 */
	protected function getAllAttributes()
	{
		$attributes = $this->attributeNames();
		$attributes = array_combine($attributes, $attributes);
		
		foreach(get_object_vars($this) as $key => $value)
		{
			if($value === NULL || is_scalar($value))
			{
				$attributes[$key] = $key;
			}
		}
		
		return $attributes;
	}
	
	/*
	 * testing for existing all scalar attributes - table and object variables
	 */
	public function hasAttribute($name) {
		return array_key_exists($name, $this->allAttributes);
	}
	
	/**
	 * @return array validation rules for model attributes. Calculate all so that only need rules for non model
	 * database attributes i.e. attributes that don't come from the models corresponding table
	 */
	public function rules($ignores = array())
	{
		$validators = $this->customValidators;
		
		// length and required
		$requiredValidator = array();
		$dateOnlyValidator = array();
		$timeOnlyValidator = array();
		$dateTimeOnlyValidator = array();
		$lengthValidators = array();
		$integerOnlyValidator = array();
		$safeValidator = array();
		foreach($this->tableSchema->columns as $column)
		{
			// ignore these columns
			if(in_array($column->name, $ignores) || $column->name == 'id' || $column->name == 'deleted' || $column->name == 'updated_by')
			{
				continue;
			}
			
			if(!$column->allowNull)
			{
				$requiredValidator[] = $column->name;
			}

			if($column->dbType == 'date')
			{
				$dateOnlyValidator[] = $column->name;
			}
			elseif($column->dbType == 'time')
			{
				$timeOnlyValidator[] = $column->name;
			}
			elseif($column->dbType == 'datetime')
			{
				$dateTimeOnlyValidator[] = $column->name;
			}
			elseif($column->type == 'integer')
			{
				$integerOnlyValidator[] = $column->name;
			}
			elseif($column->size)
			{
				$lengthValidators[$column->size][] = $column->name;
			}
			else
			{
				$safeValidator[] = $column->name;
			}
		}
		$validators = array_merge($validators, array(array(implode(',', $requiredValidator), 'required')));
		$validators = array_merge($validators, array(array(implode(',', $integerOnlyValidator), 'numerical', 'integerOnly'=>true)));
		$validators = array_merge($validators, array(array(implode(',', $dateOnlyValidator), 'date', 'format'=>'d MMM, yyyy')));
		$validators = array_merge($validators, array(array(implode(',', $timeOnlyValidator), 'date', 'format'=>'hh:mm')));
		$validators = array_merge($validators, array(array(implode(',', $dateTimeOnlyValidator), 'date', 'format'=>'d MMM yyyy, hh:mm')));
		$validators = array_merge($validators, array(array(implode(',', $safeValidator), 'safe')));
		// because search is not altering data it should be safe to allow all attributes for search scenario
		$validators = array_merge($validators, array(array(implode(',', $this->allAttributes), 'safe', 'on'=>'search')));
		foreach($lengthValidators as $size => $columns)
		{
			$validators = array_merge($validators, array(array(implode(',', $columns), 'length', 'max'=>$size)));
		}
		
		return $validators;
	}
 
	static public function getNiceNamePlural($primaryKey=null, $model=null)
	{
		if(!empty(static::$niceNamePlural))
		{
			return static::$niceNamePlural;
		}
		
		$niceName = static::getNiceName($primaryKey, $model);
		
		// alter ...ys to ...ies
		if(substr($niceName, -1) == 'y')
		{
			$niceName = substr($niceName,0,-1) .'ie';
		}
		elseif(substr($niceName, -1) == 's')
		{
			$niceName = substr($niceName,0,-1) .'se';
		}
		
		return $niceName . 's';
	}
	
	// get the nice name of the model
	static public function getNiceNameShort($primaryKey=null, $model=null)
	{
		$niceName = static::getNiceName($primaryKey, $model);
		
		if(mb_strlen($niceName) > 20)
		{
			$niceName = mb_substr(static::getNiceName($primaryKey), 0, 17) . '...';
		}
		
		return $niceName;
	}
		
	// get the nice name of the model
	static public function getNiceName($primaryKey=null, $model=null)
	{
		
		$niceName = !empty(static::$niceName)
			? static::$niceName
			: ucfirst(preg_replace('/(.* to )(.*)$/', '$2', Yii::app()->functions->sentencize(get_called_class())));
		
		// if a primary key has been given
		if($primaryKey)
		{
			$model = static::model()->findByPk($primaryKey);
		}
		
		if(!empty($model))
		{
			// need to bear in mind here that may not have necassary attributes defined so re-get the model
			// using its id and standard admin search criteria
//			$criteria = new CDbCriteria();
			$criteria = $model->searchCriteria;
			$criteria->condition = '';
			$criteria->params = array();
			$primaryKeyName = static::primaryKeyName();
			$criteria->compare("t.$primaryKeyName" , $model->$primaryKeyName);
			$attribModel = static::model()->find($criteria);
				
			// model may not be found due to criteria - join etc - development debugging clause
			if(!$attribModel)
			{
				throw new Exception;	// just a debugging exception to ensure correct attrib names etc - shouldn't ever happen live
			}

			foreach(static::getDisplayAttr() as $attribute)
			{
				$attribute = str_replace('t.', '', $attribute);
				$attributes[] = $attribModel->$attribute;
			}

			// make this our nice name - if it isn't empty
			if(!($niceName = implode(Yii::app()->params['delimiter']['display'], $attributes)) && $primaryKey)
			{
				$niceName .= " $primaryKey";
			}
		}

		return $niceName;
	}

	/**
	 * Returns foreign key attribute name within this model that references another model. This is used
	 * for creating navigational items i.e. tabs.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @param string $foreignKeys foreign keys array in this model.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel, $foreignKeys=array())
	{
		if(isset($foreignKeys[$referencesModel]))
		{
			$parentForeignKey = $foreignKeys[$referencesModel];
		}
		else
		{
			
			// make sure there is a parent first - no parent in top level
			if($referencesModel == 'Dashboard')
			{
				return;
			}
			elseif($parentForeignKey = Yii::app()->functions->uncamelize($referencesModel))
			{
				$parentForeignKey .= '_id';
			}
		}
		return $parentForeignKey;
	}
	
/*	public static function getCriteriaFromDisplayAttr(&$concat = array(), &$display = array())
	{
		$criteria = new DbCriteria();
		$model = self::model();

		foreach (static::getDisplayAttr() as $field) {
			// building display parameter which gets eval'd later
			$display[] = '{$p->' . $field . '}';

			// get attribute and array of relations to follow to attribute
			$fields = explode('->', $field);
			$attribute = array_pop($fields);
			// with relation
			if($with = implode('.', $fields))
			{
				// with model
				$criteria->with[$with] = $with;
				// pop the last relation again to ge the alias
				$alias = array_pop($copy = $fields);
				// all relations for the model
				$relations = $model->relations();
				// loop thru relations
				foreach($fields as $relationName)
				{
					// the class name of the related model
					$className = $relations[$relationName][1];
					// A model
					$relationModel = $className::model();
					// the relations of this relation
					$relations = $relationModel->relations();
				}
				// an d finally - the columns in the last relation
				$columns = $relationModel->tableSchema->columns;
			}
			else
			{
				// no relations so attrib belongs to this model
				$alias = 't';
				// columns in the attributes model
				$columns = $model->tableSchema->columns;
			}

			$criteria->order[] = "$alias.$attribute ASC";

			$column = "$alias.$attribute";

			// if non character field then need to cast and we only use varchar
			if (strpos($columns[$attribute]->dbType, 'varchar') === FALSE) {
				$column = "CONVERT($column USING utf8) COLLATE utf8_unicode_ci";
			}

			$concat[] = $column;
		}

		$criteria->order = implode(', ', $criteria->order);

		return $criteria;
	}*/

	/**
	 * Returns the listdata of specified bound column and display column.
	 * @param string $displayColumn the bound column.
	 * @param string $options can be use in sub class to specify additional attributes per option.
	 * @return listData the static model class
	 */
	public static function getListData($scopes = array(), &$options = array())
	{
		$model = static::model();
		$primaryKeyName = static::model()->tableSchema->primaryKey;
		$criteria = $model->searchCriteria;
		$criteria->condition = '';
		$displayAttr = $model::getDisplayAttr();
		$criteria->scopes = empty($scopes) ? null : $scopes;
		foreach($displayAttr as &$attr)
		{
			$order[] = "$attr ASC";
			$attr = '$p->' . $attr;
		}
		// NB: here replaceing possible alias inserted if using ActiveRecord::getDisplayAttr()
		$display = str_replace('t.', '', implode(Yii::app()->params['delimiter']['display'], $displayAttr));
		// order
		$criteria->order = implode(', ', $order);
	
		$models = static::model()->findAll($criteria);
		
		// tooltips
		if($toolTipAttribute = $model->toolTipAttribute)
		{
			foreach($models as $m)
			{
				$options[$m->$primaryKeyName] = array('data-original-title'=>$m->$toolTipAttribute);
			}
		}

		return CHtml::listData(
			$models, 
			$primaryKeyName,
			function ($p) use ($display) { return eval("return \"$display\";"); }
		);
	}

	/**
	 * Returns array of columns to be concatenated - for lists.
	 * @return array the list of columns to be concatenated
	 */
	public static function getDisplayAttr()
	{
		// choose the best column
		if(in_array('description', static::model()->tableSchema->getColumnNames()))
		{
			return array('t.description');
		}
		elseif(in_array('name', static::model()->tableSchema->getColumnNames()))
		{
			return array('t.name');
		}
		else
		{
$t=static::model()->tableSchema->getColumnNames();
			throw new Exception;	// just a debugging exception to ensure correct attrib names etc - shouldn't ever happen live
		}
	}

	/**
	 * Installs http://www.yiiframework.com/extension/attributesbackupbehavior/ to allow easy review and test if values changed
	 * and http://www.yiiframework.com/extension/save-relations-ar-behavior/ to save related records at the same time - need
	 * to add $model->setRelationRecords('relationName',$data); to controller before $model->save();
	 */
	public function behaviors()
	{
		return array(
			'AttributesBackupBehavior' => 'ext.AttributesBackupBehavior',
		);
	}
//TODO: this breaking mvc - contains controller code	
	// ensure that pk's exist for all in trail
	public function assertFromParent($modelName = null)
	{
		if(!$modelName)
		{
			$modelName = get_called_class();
		}
		
		// get trail
		$CurrentControllerName = Yii::app()->controller->id . 'Controller';
		$trail = Yii::app()->functions->multidimensional_arraySearch($CurrentControllerName::getTrail(), $modelName);
		$this->clearForwardMemory($trail);
		
		// if not at top level
		if(($trailSize = sizeof($trail)) > 1)
		{
			// loop thru trail
			foreach($trail = array_reverse($trail) as $crumb)
			{
				// if we had to jump up a level
				// skip the first one
				if($crumb == $modelName)
				{
					$model = $this;
					continue;
				}
$t = $model->attributes;
				$modelName = get_class($model);
				// get the name of the foreign key field in this model referring to the parent
				$parentForeignKeyName = $modelName::getParentForeignKey($crumb);
				// the if clause here is to exclude when model is for search on admin view and has no pk - then assume nav variables already set
				if($model->$parentForeignKeyName !== null)
				{
					// store the primary key for the model
					Controller::setUpdateId($model->$parentForeignKeyName, $crumb);
					// ensure that that at least the parents primary key is set for the admin view
					Controller::setAdminParam($parentForeignKeyName, $model->$parentForeignKeyName, $modelName);
				}

				// set the model ready for the next one
				if(!$model = $crumb::model()->findByPk($model->$parentForeignKeyName))
				{
					throw new CHttpException(400, 'Invalid request.');
				}
				
				// capture the first parent only for returning later
				if(empty($firstParentForeignKeyName))
				{
					$firstParentForeignKeyName = $parentForeignKeyName;
				}
			}
			
			// return the first parent
			return $firstParentForeignKeyName;
		}
	}

//TODO: this breaking mvc - contains controller code	
	/**
	 * Clear any memory of admin views filters, paging and sorting that arn't in the current breadcrumb trail 
	 */
	protected function clearForwardMemory(&$trail)
	{
		// loop thru all saved admin view models
		if(isset($_SESSION['admin'])) 
		{
			$t = $_SESSION['admin'];
			foreach($_SESSION['admin'] as $model => &$params)
			{
				if($model && !in_array($model, $trail))
				{
					$this->adminReset($model);
				}
			}
		}
	}

//TODO: this breaking mvc - contains controller code	
	// reset filtering, paging, and sorting - to be used after successfull creations before returning to admin view
	//
	public function adminReset($modelName = null)
	{
		if(!$modelName)
		{
			$modelName = get_class($this);
		}
		
		if(isset($_SESSION['admin'][$modelName]))
		{
			unset($_SESSION['admin'][$modelName]);
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($pagination = array())
	{
		$columnNames = $this->tableSchema->columnNames;

		// get the sort order
		foreach($this->adminColumns as $adminColumn)
		{
			if(is_array($adminColumn))
			{
				if(isset($adminColumn['name']))
				{
					$attribute = $adminColumn['name'];
				}
				else
				{
					continue;;
				}
			}
			else
			{
				$attribute = $adminColumn;
			}

			$attribute = preg_replace('/:.*/', '', $attribute);
			
			// add t alias if part of this table
			$alias = in_array($attribute, $columnNames) ? 't.' : '';
			
			$sort[$attribute] = array(
						'asc'=>" $alias$attribute ",
						'desc'=>" $alias$attribute DESC",
					);
		}

		$dataProvider = new ActiveDataProvider($this, array(
			'criteria'=>$this->getSearchCriteriaSorted($this),
			'sort'=>array('attributes'=>$sort),
			'pagination' => $pagination,
		));

		return $dataProvider;
	}

     public function defaultScope()
    {
		$defaultScope = array();
	
		// if we should be blocking soft deleted records from being returned
		if(!static::$showSoftDeletes)
		{
			// if this model has a deleted property
			if(in_array('deleted', $this->tableSchema->getColumnNames()))
			{
				$conditions = array();

				// if there is an existing condition
				if(!empty($defaultScope['condition']))
				{
					$conditions[] = $defaultScope['condition'];
				}

				// append our new condition
				$conditions[] = $this->getTableAlias(false, false) . '.deleted=0';

				// set condition in the default scope
				$defaultScope['condition'] = implode(" AND ", $conditions);
			}
		}
		
        return $defaultScope;
    }
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		// array union plus means duplicated members in the right hand array don't overwrite the left
		return
			ActiveRecord::$labelOverrides
			+ $attributeLabels + array(
				'id' => static::getNiceName(),
				'naturalKey' => static::getNiceName(),
				'select' => 'Specific values',
				'town_city' => 'Town/city',
				'state_province' => 'State/province',)
			+ Yii::app()->functions->sentencize(preg_replace('/(.* to ).*$/', '', str_replace('_id', '', str_ireplace('search', '', $this->allAttributes))));
	}

	/**
	 * Creates a field for CGridView
	 * NB: ensure that $foreingKey is included in the select in getSearchCriteria
	 * @param type $name
	 * @param type $modelName
	 * @param type $foreignKey
	 * @param type $referencesPk
	 * @return mixed 
	 */
	static function linkColumn($name, $modelName, $foreignKey, $extraParams = array())
	{
		$referencesPk = $modelName::model()->tableSchema->primaryKey;

		// if the user has at least read access
		$controllerName = "{$modelName}Controller";
		if($controllerName::checkAccess(Controller::accessRead))
		{
			// update or view
			$access = $controllerName::checkAccess(Controller::accessWrite) ? 'update' : 'view';
			// NB: want id intead of $this->tableSchema->primaryKey because yii wants a variable by the same as in the function signature
			// even though this confusing here
			// create query string with any extra paramters
			if($extraParams = http_build_query($extraParams))
			{
				$extraParams = '?'.$extraParams;
			}
			// create a link
			return array(
				'name'=>$name,
				'value'=>'CHtml::link($data->'.$name.',
					Yii::app()->createUrl("'."$modelName/$access".'", array("'.$referencesPk.'"=>$data->'.$foreignKey.'))
						."'.$extraParams.'"
				)',
				'type'=>'raw',
			);
		}
		else
		{
			// create text
			return $name;
		}
	}

// TODO: probably breaking mvc here again calling controller code	
	public function linkColumnAdjacencyList($name, &$columns, $primaryKeyName = 'id', $parentAttrib = 'parent_id')
	{
		$modelName = str_replace('View', '', get_class($this));
		$controllerName = "{$modelName}Controller";

		// add additional columns for managment of the adjacency list if user has write access
		if($controllerName::checkAccess(Controller::accessWrite))
		{
			if(!is_array($columns) || !in_array($primaryKeyName, $columns))
			{
				$columns[] = $primaryKeyName;
			}
			if(!in_array($parentAttrib, $columns))
			{
				$columns[] = $parentAttrib;
			}
		}

		// if the user has at least read access
		if($controllerName::checkAccess(Controller::accessRead))
		{
			// NB: want id intead of $this->tableSchema->primaryKey because yii wants a variable by the same as in the function signature
			// even though this confusing here
			// create a link
			$params = var_export(Controller::getAdminParams($modelName), true);
			$columns[] = array(
				'name'=>$name,
				'value'=>$modelName.'::model()->findByAttributes(array("'.$parentAttrib.'" => $data->'.$primaryKeyName.')) !== null
					? CHtml::link($data->'.$name.', Yii::app()->createUrl("'."$modelName/admin".'", array("'.$parentAttrib.'"=>$data->'.$primaryKeyName.') + '.$params.'))
					: $data->'.$name,
				'type'=>'raw',
			);
		}
		else
		{
			// create text
			$columns[] = $name;
		}
	}

	public function getSearchCriteria()
	{
		return new DbCriteria($this);
	}

	/**
	 * Sets common criteria for search.
	 * @return CDbCriteria the search/filter conditions.
	 * @param CDbCriteria $criteria the criteria object to set.
	 */
	private function getSearchCriteriaSorted()
	{
		$searchCriteria = $this->searchCriteria;
		
		$modelName = get_class($this);
		if(!isset($_GET["{$modelName}_sort"]))
		{
			// set default sort

			// if default sort order given at model level
			if($this->defaultSort)
			{
				$defaultSort = array();
	
				foreach($this->defaultSort as $key => $value)
				{
					if(is_int($key))
					{
						$defaultSort[] = "$value ASC";
					}
					else
					{
						$defaultSort[] = "$key $value";
					}
				}
				
				$searchCriteria->order = implode(', ', $defaultSort);
			}
			else
			{
				// get first display attribute to use for inititial sort
				foreach($modelName::getDisplayAttr() as $displayAttr);

				$searchCriteria->order = "$displayAttr  ASC";
			}

		}

		return $searchCriteria;
	}

	public function beforeSave()
	{
	//	$arrayForeignKeys=$this->tableSchema->foreignKeys;
		
		foreach($this->attributes as $name=>$value)
		{
			// convert empty strings to nulls if null allowed
			if(/*array_key_exists($name, $arrayForeignKeys) &&*/ $this->metadata->columns[$name]->allowNull && trim($value)=='')
			{
				$this->$name=new CDbExpression('NULL'); ;
			}
			// convert dates to mysql format - allow for nulls
			if(!empty($value) && $this->metadata->columns[$name]->dbType == 'date')
			{
				$this->$name = date('Y-m-d', strtotime($value));
			}
			// convert datetime to mysql format - allow for nulls
			if(!empty($value) && $this->metadata->columns[$name]->dbType == 'datetime')
			{
				$this->$name = date('Y-m-d H:i:s', strtotime($value));
			}
			// convert time to mysql format - allow for nulls - convert single colon without seconds to seconds
			if(!empty($value) && $this->metadata->columns[$name]->dbType == 'time')
			{
				if(substr_count($value, ':') == 1)
				{
					$value .= ':00';
				}
			}
		}

		return parent::beforeSave();
	}
	
	public function beforeValidate()
	{
		// If there is a user id column
		if(isset($this->metadata->columns['updated_by']))
		{
			$this->updated_by = Yii::app()->user->id;
		}
	
		// set any custom validators -- only if not creating
		if(!$this->isNewRecord)
		{
			$this->setCustomValidators();
		}

		return parent::beforeValidate();
	}

	public function setCustomValidators()
	{
		// force a re-read of validators
		$this->getValidators(NULL, TRUE);
	}
		
	/**
	 * Container to deal with error handling for model database calls.
	 * @param String $callback the name of the method to call.
	 * @param array $callbackArgs array of method arguments.
	 * @param array $messages where key is needle and value the message to display if needle found in catch error message.
	 * @return returns the return value from the method call.
	 */
	public function dbCallback($callback, $callbackArgs=array(), $messages=array())
	{
		$coreMessages = array('1062' => 'Duplicates are not allowed');

		$messages = $messages + $coreMessages;

		try
		{
			$return = call_user_func_array(array($this, $callback), $callbackArgs);
		}
		catch(CDbException $e)
		{
				$errorMessage = $e->getMessage();
fb($errorMessage);

			// special handling if forcing trigger failures to block an operation
			if(strpos($errorMessage, 'forced_trigger_error'))
			{
				// extact the message which is the incorrect column name - the bad table name is forced_trigger_error
				preg_match("/1054 Unknown column 'forced_trigger_error\.(.*)' in 'where clause'/", $errorMessage, $matches);
				$errorMessage = $matches[1];
			}
			else
			{
				// special handling to block parents being set to children - forcing a trigger fail on bad column name
				foreach ($messages as $needle => &$message)
				{
					// NB: do not remove the speech marks around needle - converting to string
					if(strpos($errorMessage, "$needle") !== FALSE)
					{
						$errorMessage = $message;
						break;
					}
				}
			}
				
			$this->addError(null, $errorMessage);
				
			return;
		}
if(count($m = $this->getErrors()))
{
	$t=1;
}
elseif(!$return)
{
	$t=1;
}
		return $return;
	}

	/*
	 *  Soft delete
	 */
	public function delete()
	{
		if(!$this->getIsNewRecord())
		{
			Yii::trace(get_class($this).'.delete()','system.db.ar.CActiveRecord');
			if($this->beforeDelete())
			{
				// if this model has a deleted attribute
				if(isset($this->deleted))
				{
					// soft delete
					// mark the row as deleted - increment to allow re-create and re delete later without violating unique constraints combined with deleted
					$result=$this->updateByPk($this->primaryKey, array('deleted' => 1));
				}
				// otherwise delete the row
				else
				{
					// hard delete
					$result=$this->deleteByPk($this->primaryKey)>0;;
				}

				$this->afterDelete();
				return $result;
			}
			else
				return false;
		}
		else
			throw new CDbException(Yii::t('yii','The active record cannot be deleted because it is new.'));
	}

	/*
	 *	Soft delete re-insert. When a record fails to insert, it must be due to a constraint violation. Try to update with deleted attribute
	 *  Set to 0 - hence undeleting an existing record. 
	 */
	public function insert($attributes = null) {

		try
		{
			// ensure update_by set
			if(empty($this->updated_by))
			{
				$this->updated_by = Yii::app()->user->id;
			}

			$result = parent::insert($attributes);
		}
		catch (CDbException $e)
		{
			if(isset($this->deleted))
			{
				$primaryKeyName = self::model()->tableSchema->primaryKey;
				$tableName = $this->tableName();
				$attributes = $this->attributes;
				unset($attributes['deleted']);
				if(array_key_exists($primaryKeyName, $attributes))
				{
					unset($attributes[$primaryKeyName]);
				}
				if(array_key_exists('updated_by', $attributes))
				{
					unset($attributes['updated_by']);
				}
				// get the matching row. Need to get list of attributes for search as the constraint violation columns
				// only - otherwise the other attributes will make us not find a match
				preg_match("/for key '(.*)'. The /", $e->getMessage(), $matches);
				if(isset($matches[1]))
				{
					$databaseName = Yii::app()->params['databaseName'];
					$results = Yii::app()->db->createCommand("
						SELECT COLUMN_NAME
						FROM information_schema.KEY_COLUMN_USAGE
						WHERE TABLE_SCHEMA = '$databaseName'
							AND TABLE_NAME = '$tableName'
							AND CONSTRAINT_NAME = '{$matches[1]}'")->queryAll();
					// convert to array so we can use the keys to intersect with attributes
					$keyColumns = array();
					foreach($results as $keyColumn)
					{
						$keyColumns[$keyColumn['COLUMN_NAME']] = $keyColumn['COLUMN_NAME'];
					}
				
					$attributes = array_intersect_key($attributes, $keyColumns);
				}
				if(!$attributes || !$model = self::model()->resetScope()->findByAttributes($attributes))
				{
					// unknown error i.e. not todo with already being deleted
					throw($e);
				}
				
				// if deleted
				if($model->deleted)
				{
					$this->$primaryKeyName = $model->$primaryKeyName;
					// attempt undelete
					$this->deleted = 0;
					$this->isNewRecord = FALSE;
					if($result=$this->update())
					{
						// similalate setting of other properties that insert sets
						$this->isNewRecord = TRUE;
						$this->scenario = 'update';
					}
					else
					{
						// not handled so re-throw
						throw($e);
					}
				}
				else
				{
					// not handled so re-throw
					throw($e);
				}
			}
			else
			{
				// not handled so re-throw
				throw($e);
			}
		}
		
		return $result;
	}
	
	/**
	 * Override of this necassary because _validators is private var of CModel and populated
	 * on construct or sometime before our call to dynamically add validators - where needed.
	 */
	public function getValidators($attribute=null, $force=false)
	{
		static $_validators = NULL;

		if($force)
		{
			$_validators = $this->createValidators();
		}
		elseif($_validators === NULL)
		{
			$_validators = parent::getValidators();
		}

		$validators=array();
		$scenario=$this->getScenario();
		foreach($_validators as $validator)
		{
			if($validator->applyTo($scenario))
			{
				if($attribute===null || in_array($attribute, $validator->attributes,true))
					$validators[]=$validator;
			}
		}

		return $validators;
	}
	
	/*
	 * automatically add max length attribute to inputs to save being in view file
	 * From http://www.yiiframework.com/forum/index.php/topic/3320-automatic-maxlength-attribute-for-input-fields-type-text-or-password/
	 */
	public function getAttributeMaxLength($attribute)
	{
		$validators = $this->getValidators($attribute);
		foreach($validators as $validator)
		{
			if($validator instanceof CStringValidator)
			{
				return $validator->max;
			}
		}
	}

	/*
	 * Set user defined defaults for any attributes that require them
	 */
	public function init()
	{
		if($this->isNewRecord)
		{
			// this model name
			$modelName = get_class($this);
			// get the primary key name for this model
			$primaryKeyName = $modelName::model()->tableSchema->primaryKey;
			if(isset($_GET[$primaryKeyName]))
			{
				$pk = $_GET[$primaryKeyName];
			}
			elseif(isset($_POST[$modelName][$primaryKeyName]))
			{
				$pk = $_POST[$modelName][$primaryKeyName];
			}
			else
			{
				// the parent model name
				$parentName = Controller::getParentCrumb($modelName);
				// get the name of the foreing key field in this model referring to the parent
				$parentForeignKeyName = $modelName::getParentForeignKey($parentName);
				// get the primary key in play in this context which will be referring to the parent
				if(!$pk = isset($_GET[$parentForeignKeyName]) ? $_GET[$parentForeignKeyName] : null)
				{
					$pk = isset($_POST[$modelName][$parentForeignKeyName]) ? $_POST[$modelName][$parentForeignKeyName] : null;
				}
			}

			// loop thru attributes
			$tableName = $this->tableName();
			foreach($this->safeAttributeNames as $attributeName)
			{
				// if system admin has set a default for this attribute
				if($defaultValue = DefaultValue::model()->findByAttributes(array('table'=>$tableName, 'column'=>$attributeName)))
				{
					// attempt to execute the sql
					try
					{
						$sql = $defaultValue->select;
						$command=Yii::app()->db->createCommand($sql);
						// if sql contains :pk (primary key)
						if(stripos($sql, ':pk') !== false)
						{
							if($pk !== null)
							{
								$command->bindParam(":pk", $pk);
							}
							// otherwise if error - only considered error if current context is a match otherwise could be
							// another controller creating this model
							elseif(strcasecmp(Yii::app()->controller->id, $modelName) == 0)
							{
								throw new CHttpException(403,'System admin error. The default isn\'t valid - primary key (:pk) in sql but not in this context. ');
							}
							else
							{
								continue;
							}
						}
	// TODO: this should be run of connection with restricted sys admin rights rather than main app user rights

						$this->$attributeName = $command->queryScalar();
					}
					catch (CDbException $e)
					{
						// the select failed so assume it is just text with the word 'select' in it - most likely sys admin error but 
						// deal with it anyway by just doing nothing here and the attribute gets set below anyway
						$errorMessage = $e->getMessage();
						throw new CHttpException(404,"
							System admin error. The default isn\'t valid - Database reports:
							<br>$errorMessage
						");
					}
				}
			}
		}
	}

	public function afterFind()
	{
		// format mysql
		// get columnn info from schema
		$columns = $this->tableSchema->columns;

		// add any desired formatting i.e. date to unformatted basic items
		foreach($this->attributes as $attributeName => $value)
		{
			// see if date column
			if($columns[$attributeName]->dbType == 'date')
			{
				$this->$attributeName = Yii::app()->format->date($value);
			}
			// see if date column
			if($columns[$attributeName]->dbType == 'datetime')
			{
				$this->$attributeName = Yii::app()->format->datetime($value);
			}
			// see if date column
			if($columns[$attributeName]->dbType == 'time')
			{
				$this->$attributeName = Yii::app()->format->time($value);
			}
		}
		
		parent::afterFind();
	}
	
	public function getExportColumns()
	{
        foreach($this->adminColumns as $column)
		{
			// protect against image column
			if(isset($column['imagePathExpression']))
			{
				continue;
			}

			// if no format given then use raw to stop html encoding
			$parts = explode(':', (is_string($column) ? $column : $column['name']));
			if(!isset($parts[1]))
			{
				$parts[1] = 'raw';
			}
			$columns[] = implode(':', $parts);
		}
		
		return $columns;
	}
	
	public function getHtmlId($attribute) {
		return CHtml::activeId($this, $attribute);
	}

	/*
	 * to be overidden if using mulitple models or custom validators
	 */
	public function updateSave(&$models = array()) {
		// atempt save
		$saved = $this->dbCallback('save');
		// put the model into the models array used for showing all errors
		$models[] = $this;

		return $saved;
	}

	/*
	 * to be overidden if using mulitple models
	 */

	public function createSave(&$models = array()) {
		// atempt save
		$saved = $this->dbCallback('save');
		// put the model into the models array used for showing all errors
		$models[] = $this;

		return $saved;
	}

	public function validationSQLSelect($attribute, $params)
	{
//TODO: open another database connection as this user whenever entering user entered sql.
//otherwise they can run their sql with full application access rights

		// first fake valid substitutions
		$sql = str_ireplace(':pk', '1', $this->$attribute);
		$sql = str_ireplace(':userid', '1', $sql);

		// this could before a multi-statement that might do something like createing a temporary table so in this case the sql we
		// want to deal with is the last one, and all previous ones are just to be executed
		// The last one is our sql
		// The last one is` our sql - array filter removes blank elements created eg. by ; at end
		$sqls = array_filter(explode(';', $sql));
		$sql = array_pop($sqls);
		// execute any others
		foreach($sqls as $excuteSql)
		{
			Yii::app()->db->createCommand($excuteSql)->queryAll();
		}
		
		// test if sql is valid
		try
		{
			// test validity of sql
			Yii::app()->db->createCommand($sql)->execute();
		}
		catch(CDbException $e)
		{
			$errorMessage = 'There is an error in the setup - please contact the system administrator, the database says:<br> '.$e->getMessage();
			$this->addError($attribute, $errorMessage);
		}
	}
	
	public function checkAccess($mode)
	{
		$controllerName = str_replace('View', '', get_class($this) . "Controller");
		return $controllerName::checkAccess($mode);
	}
		
}

?>