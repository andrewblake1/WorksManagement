<?php
abstract class ActiveRecord extends CActiveRecord
{
	/**
	 * Returns the listdata of specified bound column and display column.
	 * @param string $displayColumn the bound column.
	 * @param string $orderBy the order.
	 * @return listData the static model class
	 */
	public static function getListData($displayColumn)
	{
		// format models as $key=>$value with listData
		return CHtml::listData(static::model()->findAll(array('order' => $displayColumn)), 
						static::model()->tableSchema->primaryKey, $displayColumn);
	}

	/**
	 * Sets criteria for composite search i.e. a search where 1 term given with a delimter refers to more than 1 field.
	 * @param CDbCriteria $criteria the criteria object to set.
	 * @param array $columns the columns.
	 * @param string $term the term
	 */
	public static function compositeCriteria($criteria, $columns, $term)
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
		$dataProvider = new CActiveDataProvider($this, array(
			'criteria'=>$this->searchCriteria,
		));
		
		return $dataProvider;
	}
}

?>
