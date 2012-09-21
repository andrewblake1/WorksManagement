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

	// get the nice name of the model
	static public function getNiceName($primaryKey=null)
	{
		
		if(!empty(static::$niceName))
		{
			$niceName = is_string(static::$niceName)
				? static::$niceName
				: static::$niceName[Yii::app()->controller->action->id];
		}
		else
		{
			$niceName = Yii::app()->functions->sentencize(get_called_class());
		}
		
		// if a primary key has been given
		if($primaryKey)
		{
			foreach(static::getDisplayAttr() as $relationAttribute)
			{
				$attributes[] = '{$model->'.$relationAttribute.'}';
			}

			if(isset($attributes))
			{
				// get the value of that attribute
				$model = static::model()->findByPk($primaryKey);
				$attributes = implode(Yii::app()->params['delimiter']['display'], $attributes);
				eval($t ='$value = "'.$attributes.'";');
				// if the attribute is longer than 30 characters
				if(strlen($value) > 20)
				{
					// shorten to 20 characters total
					$value = substr($value, 0, 17) . '...';
				}
				// make this our nice name - if it isn't empty
				if($value)
				{
					$niceName = $value;
				}
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
		foreach(explode(Yii::app()->params['delimiter']['search'], $term) as $term)
		{
			list($key, $column) = each($columns);
			$criteria->compare($column, $term, true);
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
			$skip = false;
			foreach($trail = array_reverse($trail) as $crumb)
			{
				// if we had to jump up a level
				if($skip)
				{
					$skip = false;
					continue;
				}
				// skip the first one
				if($crumb == $modelName)
				{
					// get this model
					if(!empty($_SESSION[$crumb]['value']))
					{
						$pk = $_SESSION[$crumb]['value'];
					}
					// otherwise we don't have a model so must be admin screen via the parent
					else
					{
						// get parent name
						$crumb = current($trail);
						// get the name of the foreing key field in this model referring to the parent
						$parentForeignKey = static::getParentForeignKey($crumb);
						// see if we can now get a starting point
						if(!empty($_GET[$modelName][$parentForeignKey]))
						{
							// get parent foreign key value
							$pk = $_GET[$modelName][$parentForeignKey];
							// set session variables
							$_SESSION[$crumb]['name'] = $crumb::model()->tableSchema->primaryKey;
							$_SESSION[$crumb]['value'] = $pk;
							// make sure we skip over this in the surrounding loop as now one ahead
							$skip = true;
						}
						// otherwise code error
						else
						{
							throw Exception();
						}
					}
					$model = $crumb::model()->findByPk($pk);
					continue;
				}

				// get the name of the foreing key field in this model referring to the parent
				$primaryKeyName = static::getParentForeignKey($crumb);
				// ensure the primary key is set for this parent crumb
				$_SESSION[$crumb]['name'] = $crumb::model()->tableSchema->primaryKey;

				$_SESSION[$crumb]['value'] = $model->$primaryKeyName;
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
	static function linkColumn($name, $modelName, $foreignKey, $referencesPk='id')
	{
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
		}
if(count($m = $this->getErrors()))
{
	$s=1;
}
		return $return;
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
		// loop thru attributes
		foreach($this->attributeNames() as $attributeName)
		{
			// if system admin has set a default for this attribute
			if($defaultValue = DefaultValue::model()->findByAttributes(array('table'=>$this->tableName(), 'column'=>$attributeName)))
			{
				// if this is likely to be an sql select
				if(stripos($defaultValue->select, 'SELECT') !== false)
				{
					// attempt to execute the sql
					try
					{
// TODO: this should be run of connection with restricted sys admin rights rather than main app user rights
						$this->$attributeName = Yii::app()->db->createCommand($defaultValue->select)->queryScalar();
						continue;
					}
					catch (CDbException $e)
					{
						// the select failed so assume it is just text with the word 'select' in it - most likely sys admin error but 
						// deal with it anyway by just doing nothing here and the attribute gets set below anyway
					}
				}
				
				// set to the value of the select column
				$this->$attributeName = $defaultValue->select;
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
		}
		
		parent::afterFind();
	}
}

?>