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
	 */
	static $defaultScope = array();

	static public function getNiceName($primaryKey=null)
	{
		// get the nice name of the model if not set in calling class
		$niceName = empty(static::$niceName)
			? Yii::app()->functions->sentencize(get_called_class())
			: static::$niceName;
		
		// if a primary key has been given
		if($primaryKey)
		{
			// if there is description or name attribute in this model
			$attributeNames = static::model()->attributeNames();
			if(in_array('description', $attributeNames))
			{
				$attributeName = 'description';
			}
			elseif(in_array('name', $attributeNames))
			{
				$attributeName = 'name';
			}
			if(isset($attributeName))
			{
				// get the value of that attribute
				$model = static::model()->findByPk($primaryKey);
				$value = $model->$attributeName;
				// if the attribute is longer than 30 characters
				if(strlen($value) > 20)
				{
					// shorten to 20 characters total
					$value = substr($value, 0, 27) . '...';
				}
				// make this our nice name
				$niceName = $value;
			}
		}
		
		return $niceName;
	}
	/**
	 * Returns foreign key attribute name within this model that references another model. This is used
	 * for creating navigational items i.e. tabs.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel, $parentForeignKeys=array())
	{
		return isset($parentForeignKeys[$referencesModel])
			? $parentForeignKeys[$referencesModel]
			: Yii::app()->functions->uncamelize($referencesModel) . '_id';
	}
	
	/**
	 * Returns the listdata of specified bound column and display column.
	 * @param string $displayColumn the bound column.
	 * @return listData the static model class
	 */
	public static function getListData()
	{
		// format models as $key=>$value with listData
		$criteria=new CDbCriteria;
		
		// key will contain either a number or a foreign key field in which case field will be the lookup value
		foreach(static::getDisplayAttr() as $key => $field)
		{
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
		}

		$criteria->order = implode(', ', $criteria->order);

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
				'id',
				"CONCAT_WS('$delimiter',".implode(',', $concat_ws).") AS naturalKey",
			);
		
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
	public static function getDisplayAttr($displayColumn='description')
	{
		return array($displayColumn);
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
		
		$dataProvider = new CActiveDataProvider($this, array(
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
		// add in any run time scopes accessibly to outside classes
		$defaultScope = static::$defaultScope;
	
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
		$arrayForeignKeys=$this->tableSchema->foreignKeys;
		
		foreach ($this->attributes as $name=>$value)
		{
			if (array_key_exists($name, $arrayForeignKeys) && $this->metadata->columns[$name]->allowNull && trim($value)=='')
			{
				$this->$name=NULL;
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

	public function getAdminColumns()
	{
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

}

?>