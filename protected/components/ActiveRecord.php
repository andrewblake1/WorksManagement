<?php
abstract class ActiveRecord extends CActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchStaff;
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
	 * @var array defaultScopes that can be set and used at run time. This is basically a global
	 * variable within the class context that allows other classes to filter query results without
	 * having to pass thru several method arguments. Not tigtly coupled but convenient.
	 
	static $defaultScope = array();/*

	/**
	 * Returns the static model of the specified AR class.
	 * @return Client the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model(get_called_class());
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
		
		return $niceName . 's';
	}
	// get the nice name of the model
	static public function getNiceName($primaryKey=null, $model=null)
	{
		
		if(!empty(static::$niceName))
		{
			$niceName = static::$niceName;
/*			$niceName = is_string(static::$niceName)
				? static::$niceName
				: static::$niceName[Yii::app()->controller->action->id];*/
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

			// if the attribute is longer than 30 characters
			if(strlen($attributes) > 20)
			{
				// shorten to 20 characters total
				$attributes = substr($attributes, 0, 17) . '...';
			}

			// make this our nice name - if it isn't empty
			if($attributes)
			{
				$niceName = $attributes;
			}
		}
		
		return $niceName;
	}
	/**
	 * Returns foreign key attribute name within this model that references another model. This is used
	 * for creating navigational items i.e. tabs.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @param string $referencesModel foreign keys array in this model.
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
/*		{
			// if we are using a foreign key lookup
			if(!is_numeric($key))
			{
				$criteria->with[] = $key;
				$criteria->order[] = "$key.$field asc";
				$concat_ws[] = "$key.$field";
			}
			else
			{
				$criteria->order[] = "$field asc";
				$concat_ws[] = $field;
			}
		}*/

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
	
	// ensure that pk's exist for all in trail
	public function assertFromParent()
	{
		// this model name
		$modelName = get_called_class();
		
		// get trail
		$trail = Yii::app()->functions->multidimensional_arraySearch(Yii::app()->params['trail'], $modelName);
		
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

				$modelName = get_class($model);
				// get the name of the foreing key field in this model referring to the parent
				$primaryKeyName = $modelName::getParentForeignKey($crumb);
				// ensure the primary key is set for this parent crumb
				$_SESSION[$crumb]['name'] = $crumb::model()->tableSchema->primaryKey;
				// the if clause here is to exclude when model is for search on admin view and has no pk - then assume session variables already set
				if($model->$primaryKeyName !== null)
				{
					$_SESSION[$crumb]['value'] = $model->$primaryKeyName;
					// ensure that that at least the parents primary key is set for the admin view
//					$_SESSION['actionAdminGet'][$modelName ][$crumb] = $_SESSION[$crumb]['value'];
					$_SESSION['actionAdminGet'][$modelName ][$primaryKeyName] = $_SESSION[$crumb]['value'];
				}
				// set the model ready for the next one
				$model = $crumb::model()->findByPk($_SESSION[$crumb]['value']);
				
				// capture the first parent only for returning later
				if(empty($parentForeignKey))
				{
					$parentForeignKey = $primaryKeyName;
				}
			}
			
			// return the first parent
			return $parentForeignKey;
		}
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// get the sort order
		foreach($this->searchSort as $attribute)
		{
			$sort[$attribute] = array(
						'asc'=>" $attribute ",
						'desc'=>" $attribute DESC",
					);
		}
		
		// add searchStaff
		$sort['searchStaff'] = array(
					'asc'=>" searchStaff ",
					'desc'=>" searchStaff DESC",
				);

		// add all other attributes
		$sort[] = '*';
		
		$dataProvider = new ActiveDataProvider($this, array(
			'criteria'=>self::getSearchCriteria($this),
			'sort'=>array('attributes'=>$sort),
			'pagination' => false,
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
		return array('searchStaff');
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		// array union plus means duplicated members in the right hand array don't overwrite the left
		return ActiveRecord::$labelOverrides + $attributeLabels + array(
			'naturalKey' => $attributeLabels[$this->tableSchema->primaryKey],
			'searchStaff' => 'Staff, First/Last/Email',
			'staff_id' => 'Staff, First/Last/Email',
			'description' => 'Description',
			'deleted' => 'Deleted',
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
	static function linkColumn($name, $modelName, $foreignKey, $referencesPk=null)
	{
		if($referencesPk === null)
		{
			$referencesPk = $modelName::model()->tableSchema->primaryKey;
		}

		// if the user has at least read access
		$controllerName = "{$modelName}Controller";
		if($controllerName::checkAccess(Controller::accessRead))
		{
			// create a link
			return array(
				'name'=>$name,
				'value'=>'CHtml::link($data->'.$name.',
					Yii::app()->createUrl("'.$modelName.'/update", array("'.$referencesPk.'"=>$data->'.$foreignKey.'))
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
	
	protected function linkThisColumn($name)
	{
		return self::linkColumn($name, get_class($this), $this->tableSchema->primaryKey, $this->tableSchema->primaryKey);
	}

	/**
	 * Sets common criteria for search.
	 * @return CDbCriteria the search/filter conditions.
	 * @param CDbCriteria $criteria the criteria object to set.
	 */
	public function getSearchCriteria($model)
	{
		$searchCriteria = $model->searchCriteria;
		
		// if this model has a deleted property
		if(in_array('staff_id', $model->tableSchema->getColumnNames()))
		{
			$this->compositeCriteria($searchCriteria, array('staff.first_name','staff.last_name','staff.email'), $model->searchStaff);
			$searchCriteria->with[] = 'staff';
			$delimiter = Yii::app()->params['delimiter']['display'];
			$searchCriteria->select[] = "CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff";
		}

		if(!isset($_GET[get_class($model).'_sort']))
		{
			// this if clause in case a view with no primary key
			$searchCriteria->order = 't.'.$model->tableSchema->primaryKey." DESC";
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
		// If there is a staff id column
		if(isset($this->metadata->columns['staff_id']))
		{
			$this->staff_id = Yii::app()->user->id;
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
			foreach ($messages as $needle => &$message)
			{
				// NB: do not remove the speech marks around needle - converting to string
				if(strpos($errorMessage, "$needle") !== FALSE)
				{
					$errorMessage = $message;
					break;
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
					$this->deleted = Yii::app()->db->createCommand('SELECT MAX(deleted) + 1 FROM '.$this->tableName())->queryScalar();
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
			$modelName = get_class($this);
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
							// get the primary key in play in this context which will be referring to the parent
							$pk = $_SESSION[Yii::app()->controller->getParentCrumb($modelName)]['value'];
							if($pk !== null)
							{
								$command->bindParam(":pk", $pk, PDO::PARAM_STR);
							}
							// otherwise error
							else
							{
								throw new CHttpException(403,'System admin error. The default isn\'t valid - primary key (:pk) in sql but not in this context. ');
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

}

?>