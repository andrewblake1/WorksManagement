<?php

/**
 * This is the model class for table "tbl_resource_data".
 *
 * The followings are the available columns in table 'tbl_resource_data':
 * @property string $id
 * @property string $planning_id
 * @property string $level
 * @property integer $resource_id
 * @property integer $resource_to_supplier_id
 * @property integer $quantity
 * @property string $hours
 * @property string $start
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Planning $planning
 * @property Planning $level0
 * @property User $updatedBy
 * @property ResourceToSupplier $resource
 * @property ResourceToSupplier $resourceToSupplier
 * @property TaskToResource[] $taskToResources
 * @property TaskToResource[] $taskToResources1
 * @property TaskToResource[] $taskToResources2
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
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('planning_id, level, resource_id, quantity, hours', 'required'),
			array('resource_id, resource_to_supplier_id, quantity', 'numerical', 'integerOnly'=>true),
			array('planning_id, level', 'length', 'max'=>10),
			array('start, hours', 'date', 'format'=>'H:m'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, planning_id, level, resource_id, resource_to_supplier_id, quantity, hours, start', 'safe', 'on'=>'search'),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'resource' => array(self::BELONGS_TO, 'ResourceToSupplier', 'resource_id'),
            'resourceToSupplier' => array(self::BELONGS_TO, 'ResourceToSupplier', 'resource_to_supplier_id'),
            'taskToResources' => array(self::HAS_MANY, 'TaskToResource', 'resource_data_id'),
            'taskToResources1' => array(self::HAS_MANY, 'TaskToResource', 'resource_id'),
            'taskToResources2' => array(self::HAS_MANY, 'TaskToResource', 'level'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'planning_id' => 'Planning',
			'level' => 'Level',
			'resource_id' => 'Resource Type',
			'resource_to_supplier_id' => 'Resource Type To Supplier',
			'hours' => 'Hours',
			'start' => 'Start',
		));
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new DbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('planning_id',$this->planning_id,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('resource_to_supplier_id',$this->resource_to_supplier_id);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('hours',Yii::app()->format->toMysqlTime($this->hours));
		$criteria->compare('start',Yii::app()->format->toMysqlTime($this->start));
		$criteria->compare('updated_by',$this->updated_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}