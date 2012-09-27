<?php

/**
 * This is the model class for table "resource_data".
 *
 * The followings are the available columns in table 'resource_data':
 * @property string $id
 * @property string $planning_id
 * @property string $level
 * @property integer $resource_type_id
 * @property integer $resource_type_to_supplier_id
 * @property integer $quantity
 * @property integer $hours
 * @property string $start
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Planning $planning
 * @property Planning $level0
 * @property Staff $staff
 * @property ResourceTypeToSupplier $resourceType
 * @property ResourceTypeToSupplier $resourceTypeToSupplier
 * @property TaskToResourceType[] $taskToResourceTypes
 * @property TaskToResourceType[] $taskToResourceTypes1
 * @property TaskToResourceType[] $taskToResourceTypes2
 */
class ResourceData extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ResourceData the static model class
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
		return 'resource_data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('planning_id, level, resource_type_id, quantity, staff_id', 'required'),
			array('resource_type_id, resource_type_to_supplier_id, quantity, staff_id', 'numerical', 'integerOnly'=>true),
			array('planning_id, level', 'length', 'max'=>10),
			array('start, hours', 'date', 'format'=>'H:m'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, planning_id, level, resource_type_id, resource_type_to_supplier_id, quantity, hours, start, staff_id', 'safe', 'on'=>'search'),
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
			'planning' => array(self::BELONGS_TO, 'Planning', 'planning_id'),
			'level0' => array(self::BELONGS_TO, 'Planning', 'level'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'resourceType' => array(self::BELONGS_TO, 'ResourceTypeToSupplier', 'resource_type_id'),
			'resourceTypeToSupplier' => array(self::BELONGS_TO, 'ResourceTypeToSupplier', 'resource_type_to_supplier_id'),
			'taskToResourceTypes' => array(self::HAS_MANY, 'TaskToResourceType', 'resource_data_id'),
			'taskToResourceTypes1' => array(self::HAS_MANY, 'TaskToResourceType', 'resource_type_id'),
			'taskToResourceTypes2' => array(self::HAS_MANY, 'TaskToResourceType', 'level'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'planning_id' => 'Planning',
			'level' => 'Level',
			'resource_type_id' => 'Resource Type',
			'resource_type_to_supplier_id' => 'Resource Type To Supplier',
			'quantity' => 'Quantity',
			'hours' => 'Hours',
			'start' => 'Start',
			'staff_id' => 'Staff',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('planning_id',$this->planning_id,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('resource_type_id',$this->resource_type_id);
		$criteria->compare('resource_type_to_supplier_id',$this->resource_type_to_supplier_id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('hours',Yii::app()->format->toMysqlTime($this->hours));
		$criteria->compare('start',Yii::app()->format->toMysqlTime($this->start));
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}