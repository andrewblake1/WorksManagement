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
	 * Returns the listdata of specified bound column and display column.
	 * @param string $displayColumn the bound column.
	 * @return listData the static model class
	 */
	public static function getListData()
	{
		// format models as $key=>$value with listData
		$criteria=new CDbCriteria;
		
		// loop thru display attribute array for this model
		foreach(static::getDisplayAttr() as $key1 => $value1)
		{
			// if this attribute is an array
			if(is_array($value1))
			{
				// $key1 is the relation and $value the column list
				foreach($value1 as &$value2)
				{
					$criteria->order[] = "$key1.$value2 ASC";
					$concat[] = "$key1.$value2";
				}
				$criteria->with[] = $key1;
			}
			// otherwise
			else
			{
				// $value1 is column
				$criteria->order[] = "$value1 ASC";
				$concat[] = $value1;
			}
		}
		
		$criteria->order = implode(' ', $criteria->order);
		$firstAttribName = Yii::app()->functions->camelize($concat[0]);

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
				'id',
				"CONCAT_WS('$delimiter',".implode(',', $concat).") AS naturalKey",
			);
		
		return CHtml::listData(
			static::model()->findAll($criteria), 
			static::model()->tableSchema->primaryKey,
			'naturalKey'
		);
	}
/*	public static function getListData()
	{
		// format models as $key=>$value with listData
		$criteria=new CDbCriteria;
		$criteria->order = '
			first_name ASC,
			last_name ASC,
			email ASC'
		;
		$criteria->select=array(
			'id',
			"CONCAT_WS('$delimiter',
				first_name,
				last_name,
				email
				) AS staff",
		);
		
		return CHtml::listData(
			self::model()->findAll($criteria), 
			self::model()->tableSchema->primaryKey, 'staff'
		);
	}
	public static function getListData($displayColumn='description')
	{
		// format models as $key=>$value with listData
		$criteria=new CDbCriteria;
		$criteria->scopes=array('notDeleted');
		$criteria->order = "$displayColumn ASC";
		$criteria->select=array(
			static::model()->tableSchema->primaryKey,
			"$displayColumn",
		);
		
		return CHtml::listData(
			self::model()->findAll($criteria), 
			self::model()->tableSchema->primaryKey, $displayColumn
		);
	}*/

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
//			'EActiveRecordRelationBehavior' => 'ext.activerecord-relation.EActiveRecordRelationBehavior'
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
			'criteria'=>$this->searchCriteria,
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
		$defaultScope = array();
	
		// if this model has a deleted property
		if(in_array('deleted', $this->tableSchema->getColumnNames()))
		{
			$defaultScope['condition'] = $this->getTableAlias(false, false).'.deleted=0';
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
		return $attributeLabels + array(
			'naturalKey' => $attributeLabels[$this->tableSchema->primaryKey],
			'searchStaff' => 'Staff (First/Last/Email)',
			'staff_id' => 'Staff (First/Last/Email)',
			'description' => 'Description',
			'deleted' => 'Deleted',
		);
	}

}

?>
