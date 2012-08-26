<?php

/**
 * This is the model class for view "v_generic".
 *
 * The followings are the available columns in view 'v_generic':
 * @property string $id
 * @property string $parent_id the id from prmimary key in the parent table to restrict search by
 * @property string $description
 * @property string $value this technically is mixed type if int, float, date, time, text
 *
 * The followings are the available model relations:
 * @property Staff $staff
 */
abstract class ViewGenericActiveRecord extends ActiveRecord
{
	public function primaryKey()
	{
		return 'id';
	}
		
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
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'description' => 'Generic type',
			'value' => 'Value',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria=new CDbCriteria;

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