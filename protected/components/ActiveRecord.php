<?php
abstract class ActiveRecord extends RangeActiveRecord
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
	 * Returns the static model of the specified AR class.
	 * @return Client the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model(get_called_class());
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_' . Yii::app()->functions->uncamelize(get_class($this));
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
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// because only setting search attribues names herer and not actually assigning variables
		// it should be fine to use all object varialbles. This technically means that things like defaultSort could
		// be injected so we need to be aware and remove these here or not use this if there is potential issue
		
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge($this->customValidators, array(array(implode(',', $this->allAttributes), 'safe', 'on'=>'search')));
	}
 
	static public function evalDisplayAttr($model)
	{
		$attributes = array();
		
		foreach(static::getDisplayAttr() as $relationAttribute)
		{
			$eval = '$model';

			// would just eval the whole thing but can spit php notice if non existing child class hence this longer way
			foreach(explode('->', $relationAttribute) as $relationAttribute)
			{
				eval('$value = '.$eval.'->'.$relationAttribute.';');
				if(empty($value))
				{
					break;
				}
				else
				{
					$eval .= '->'.$relationAttribute;
				}
			}

			if(!empty($value))
			{
				$attributes[] = $value;
			}
		}

		// get the value of that attribute
		$attributes = implode(Yii::app()->params['delimiter']['display'], $attributes);
		
		return $attributes;
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
	static public function getNiceName($primaryKey=null, $model=null)
	{
		
		if(!empty(static::$niceName))
		{
			$niceName = static::$niceName;
		}
		else
		{
			$niceName = Yii::app()->functions->sentencize(get_called_class());
		}
		
		// if a primary key has been given
		if($primaryKey)
		{
			$model = static::model()->findByPk($primaryKey);
		}
		
		if(!empty($model))
		{
			$attributes = static::evalDisplayAttr($model);

/*			// if the attribute is longer than 30 characters
			if(mb_strlen($attributes) > 20)
			{
				// shorten to 20 characters total
				$attributes = mb_substr($attributes, 0, 17) . '...';
			}*/

			// make this our nice name - if it isn't empty
			if($attributes)
			{
				$niceName = $attributes;
			}
			elseif($primaryKey)
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
			if($parentForeignKey = Yii::app()->functions->uncamelize($referencesModel))
			{
				$parentForeignKey .= '_id';
			}
		}
		return $parentForeignKey;
	}
	
	/**
	 * Returns the listdata of specified bound column and display column.
	 * @param string $displayColumn the bound column.
	 * @return listData the static model class
	 */
	public static function getListData($scopes = array())
	{
		// format models as $key=>$value with listData
		$criteria=new CDbCriteria;
		
		// key will contain either a number or a foreign key field in which case field will be the lookup value
		foreach(static::getDisplayAttr() as $field)
		{
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
			$concat_ws[] = "$alias.$attribute";
		}

		$criteria->order = implode(', ', $criteria->order);

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
				't.'.static::model()->tableSchema->primaryKey,
				"CONCAT_WS('$delimiter',".implode(',', $concat_ws).") AS naturalKey",
			);
		$criteria->scopes = empty($scopes) ? null : $scopes;
		
		return CHtml::listData(
			static::model()->findAll($criteria), 
			static::model()->tableSchema->primaryKey,
			'naturalKey'
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
			return array('description');
		}
		elseif(in_array('name', static::model()->tableSchema->getColumnNames()))
		{
			return array('name');
		}
		else
		{
		$t=static::model()->tableSchema->getColumnNames();
			throw new Exception;	// just a debugging exception to ensure correct attrib names etc - shouldn't ever happen live
		}
	}

	/**
	 * Sets criteria for composite search i.e. a search where 1 term given with a delimter refers to more than 1 field.
	 * @param CDbCriteria $criteria the criteria object to set.
	 * @param array $columns the columns.
	 * @param string $term the term
	 */
	public function compositeCriteria($criteria, $columns, $term)
	{
/*		foreach(explode(Yii::app()->params['delimiter']['search'], $term) as $term)
		{
			list($key, $column) = each($columns);
			$criteria->compare($column, $term, true);
		}*/
		
		// if something has been entered
		if($term)
		{
			// protect against possible injection
			$concat = "CONCAT_WS(' ', ". implode(', ', $columns) . ")";
			$cntr = 0;
			$criteria->params = array();
			foreach($terms = explode(' ', $term) as $term)
			{
				$term = trim($term);
				$paramName = ":param$cntr";
				$criteria->condition .= ($criteria->condition ? " AND " : '')."$concat LIKE $paramName";
				$criteria->params[$paramName] = "%$term%";
				$cntr++;
			}
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
		// get the sort order
		foreach($this->searchSort as $attribute)
		{
			$sort[$attribute] = array(
						'asc'=>" $attribute ",
						'desc'=>" $attribute DESC",
					);
		}
		
		// add searchUser
		$sort['searchUser'] = array(
					'asc'=>" searchUser ",
					'desc'=>" searchUser DESC",
				);
$t=self::getSearchCriteria($this);
$t=$this->attributes;
		// add all other attributes
		$sort[] = '*';
		$dataProvider = new ActiveDataProvider($this, array(
			'criteria'=>self::getSearchCriteria($this),
			'sort'=>array('attributes'=>$sort),
			'pagination' => $pagination,
		));
		return $dataProvider;
	}

 	/**
	 * Named scopes to use when searching.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
   public function scopes()
    {
        return array(
 /*           'recently'=>array(
                'order'=>'create_time DESC',
                'limit'=>5,
            ),*/
        );
    }
	
    public function defaultScope()
    {
//		// add in any run time scopes accessibly to outside classes
//		$defaultScope = static::$defaultScope;
		$defaultScope = array();
	
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
		
		
        return $defaultScope;
    }

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		$t = $this->rules();
		foreach ($this->rules() as $rule)
		{
			if(isset($rule['on']) && $rule['on'] == 'search' && $rule[1] == 'safe')
			{
				$theseColumns = $this->tableSchema->columnNames;
				$sorts = explode(',', str_replace(' ', '', $rule[0]));
				foreach($sorts as $key => &$value)
				{
					if(in_array($value, $theseColumns))
					{
						$value = "t.$value";
					}
				}
				return $sorts;
			}
		}

		return array();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		// array union plus means duplicated members in the right hand array don't overwrite the left
		return ActiveRecord::$labelOverrides + $attributeLabels + array(
			'id' => static::getNiceName(),
			'naturalKey' => static::getNiceName(),
			'searchUser' => 'User, First/Last/Email',
			'updated_by' => 'User, First/Last/Email',
			'description' => 'Description',
			'deleted' => 'Deleted',
			'parent_id' => 'Parent',
			'alias' => 'Alias',
			'quantity' => 'Quantity',
			'minimum' => 'Minimum',
			'maximum' => 'Maximum',
			'select' => 'Specific values',
			'name' => 'Name',
			'first_name' => 'First name',
			'last_name' => 'Last name',
			'role' => 'Role',
			'email' => 'Email',
			'address_line_1' => 'Address line 1',
			'address_line_2' => 'Address line 2',
			'post_code' => 'Post code',
			'town_city' => 'Town/city',
			'state_province' => 'State/province',
			'country' => 'Country',
			'phone_mobile' => 'Phone mobile',
			'phone_home' => 'Phone home',
			'phone_work' => 'Phone work',
			'phone_fax' => 'Phone fax',
			'level' => 'Level',
			'hours' => 'Hours',
			'start' => 'Start',
			'quantity_tooltip' => 'Quantity tooltip',
			'selection_tooltip' => 'Selection tooltip',
		);
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

		// add addtional columns for managment of the adjacency list if user has write access
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
	
	protected function linkThisColumn($name)
	{
		// NB: want id intead of $this->tableSchema->primaryKey because yii wants a variable by the same as in the function signature
		// even though this confusing here
//		return self::linkColumn($name, get_class($this), $this->tableSchema->primaryKey);
		return $name;
	}

	/**
	 * Sets common criteria for search.
	 * @return CDbCriteria the search/filter conditions.
	 * @param CDbCriteria $criteria the criteria object to set.
	 */
	public function getSearchCriteria($model)
	{
		$searchCriteria = $model->searchCriteria;
		
		// if this model has a updated_by property
/*		if(in_array('updated_by', $model->tableSchema->getColumnNames()))
		{
			$this->compositeCriteria($searchCriteria, array('updatedBy.first_name','updatedBy.last_name','updatedBy.email'), $model->searchUser);
			$searchCriteria->with[] = 'updatedBy';
			$delimiter = Yii::app()->params['delimiter']['display'];
			$searchCriteria->select[] = "CONCAT_WS('$delimiter', updatedBy.first_name, updatedBy.last_name, updatedBy.email) AS searchUser";
		}*/

		$modelName = get_class($model);
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
				foreach($modelName::getDisplayAttr() as $key => $displayAttr);
				
				if(preg_match('/(((.*)->)?(\w*))->(\w*)$/', $displayAttr, $matches))
				{
					$searchCriteria->order = "{$matches[4]}.{$matches[5]}  ASC";
				}
				else
				{
					$searchCriteria->order = "t.$displayAttr  ASC";
				}
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

		return parent::beforeValidate();
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
	$s=1;
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
					$this->deleted = 1;
					$result=$this->save();
				}
				// otherwise delete the row
				else
				{
					// hard delete
					$result=$this->deleteByPk($this->getPrimaryKey())>0;;
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
			$result = parent::insert($attributes);
		}
		catch (CDbException $e)
		{
			if(isset($this->deleted))
			{
				$primaryKeyName = self::model()->tableSchema->primaryKey;
				$attributes = $this->attributes;
				unset($attributes['deleted']);
				if(array_key_exists($primaryKeyName, $attributes))
				{
					unset($attributes[$primaryKeyName]);
				}
//				unset($attributes[$primaryKeyName]);
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
							AND CONSTRAINT_NAME = '{$matches[1]}'")->queryAll();
					// convert to array so we can use the keys to intersect with attributes
					$keyColumns = array();
					foreach($results as $keyColumn)
					{
						$keyColumns[$keyColumn['COLUMN_NAME']] = $keyColumn['COLUMN_NAME'];
					}
				
					$attributes = array_intersect_key($attributes, $keyColumns);
				}
				if(!$model = self::model()->resetScope()->findByAttributes($attributes))
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
			$_validators = parent::getValidators($attribute);
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
				return $validator->max;
		}
	}

	/*
	 * Set user defined defaults for any attributes that require them
	 */
	public function init()
	{
//$t = $this->safeAttributeNames;
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
			foreach($this->safeAttributeNames as $attributeName)
			{
				// if system admin has set a default for this attribute
				if($defaultValue = DefaultValue::model()->findByAttributes(array('table'=>$this->tableName(), 'column'=>$attributeName)))
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
								$command->bindParam(":pk", $pk, PDO::PARAM_STR);
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
			$columns[] = is_string($column) ? $column : $column['name'];
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

	public function validationSQL($attribute, $params)
	{
//TODO: open another database connection as this user whenever entering user entered sql.
//otherwise they can run their sql with full application access rights

		// first fake valid substitutions
		$sql = str_ireplace(':pk', '1', $this->$attribute);
		$sql = str_ireplace(':userid', '1', $sql);

		// test if sql is valid
		try
		{
			// test validity of sql
			Yii::app()->db->createCommand($sql)->queryAll();
		}
		catch(Exception $e)
		{
			$errorMessage = 'There is an error in the setup - please contact the system administrator, the database says:<br> '.$e->getMessage();
			$this->addError($attribute, $errorMessage);
		}
	}
	
}

?>