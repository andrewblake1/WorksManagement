<?php

/**
 * This is the model class for "ViewCustomFieldActiveRecord".
 *
 * The followings are the available columns in 'ViewCustomFieldActiveRecord':
 * @property string $id
 * @property string $parent_id the id from prmimary key in the parent table to restrict search by
 * @property string $description
 * @property string $value this technically is mixed type if int, float, date, time, text
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 */
abstract class ViewCustomFieldActiveRecord extends ViewActiveRecord
{
	
		
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('parent_id, description, value', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array()) {
		return parent::attributeLabels(array(
			'id' => 'Custom field',
			'description' => 'Custom field',
			'value' => 'Value',
		) + $attributeLabels);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria=new DbCriteria;

		$criteria->compare('t.parent_id',$this->parent_id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.value',$this->value,true);

		$criteria->select=array(
			't.description',
			't.value',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'value';
		
		return $columns;
	}
}

?>