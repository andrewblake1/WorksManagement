<?php

/**
 * This is the model class for table "standard_drawing_adjacency_list".
 *
 * The followings are the available columns in table 'standard_drawing_adjacency_list':
 * @property integer $id
 * @property integer $parent_id
 * @property integer $child_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property StandardDrawing $parent
 * @property StandardDrawing $child
 * @property Staff $staff
 */
class StandardDrawingAdjacencyList extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StandardDrawingAdjacencyList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'standard_drawing_adjacency_list';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, child_id, staff_id', 'required'),
			array('parent_id, child_id, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parent_id, child_id, staff_id', 'safe', 'on'=>'search'),
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
			'parent' => array(self::BELONGS_TO, 'StandardDrawing', 'parent_id'),
			'child' => array(self::BELONGS_TO, 'StandardDrawing', 'child_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
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
			'child_id' => 'Child',
			'staff_id' => 'Staff',
		);
	}

	public static function getDisplayAttr()
	{
		return array(
			'parent->id',
			'parent->description',
			'parent->alias',
		);
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
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}