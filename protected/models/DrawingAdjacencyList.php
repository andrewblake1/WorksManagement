<?php

/**
 * This is the model class for table "tbl_drawing_adjacency_list".
 *
 * The followings are the available columns in table 'tbl_drawing_adjacency_list':
 * @property integer $id
 * @property integer $parent_id
 * @property integer $child_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Drawing $parent
 * @property Drawing $child
 * @property User $updatedBy
 */
class DrawingAdjacencyList extends ActiveRecord
{
	public $searchParent;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DrawingAdjacencyList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('parent_id, child_id, updated_by', 'required'),
			array('parent_id, child_id, updated_by', 'numerical', 'integerOnly'=>true),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'parent' => array(self::BELONGS_TO, 'Drawing', 'parent_id'),
            'child' => array(self::BELONGS_TO, 'Drawing', 'child_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent_id' => 'Parent',
			'searchParent' => 'Parent',
			'child_id' => 'Child',
			'updated_by' => 'User',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.*',	// needed for delete and update buttons
			"CONCAT_WS('$delimiter',
				parent.id,
				parent.description
				parent.alias
			) AS searchParent",
		);

		// with
		$criteria->with = array(
			'parent',
		);
		
		return $criteria;
	}

	public static function getDisplayAttr()
	{
		return array('searchParent');
	}
 
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('child_id',$this->child_id);
		$criteria->compare('updated_by',$this->updated_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}