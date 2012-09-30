<?php

/**
 * This is the model class for table "resource_type".
 *
 * The followings are the available columns in table 'resource_type':
 * @property integer $id
 * @property string $description
 * @property string $unit_price
 * @property integer $resourcecategory_id
 * @property integer $maximum
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ResourceData[] $resourceDatas
 * @property Resourcecategory $resourcecategory
 * @property Staff $staff
 * @property ResourceTypeToSupplier[] $resourceTypeToSuppliers
 * @property TaskTypeToResourceType[] $taskTypeToResourceTypes
 */
class ResourceType extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'resource_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, staff_id', 'required'),
			array('resourcecategory_id, maximum, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('unit_price', 'length', 'max'=>7),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, unit_price, resourcecategory_id, maximum, deleted, searchStaff', 'safe', 'on'=>'search'),
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
			'resourceDatas' => array(self::HAS_MANY, 'ResourceData', 'resource_type_id'),
			'resourcecategory' => array(self::BELONGS_TO, 'Resourcecategory', 'resourcecategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'resourceTypeToSuppliers' => array(self::HAS_MANY, 'ResourceTypeToSupplier', 'resource_type_id'),
			'taskTypeToResourceTypes' => array(self::HAS_MANY, 'TaskTypeToResourceType', 'resource_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Resource type',
			'resourcecategory_id' => 'Resource category',
			'unit_price' => 'Unit price',
			'maximum' => 'Maximum',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',
			't.description',
			't.unit_price',
			't.maximum',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.unit_price',$this->unit_price);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('t.resourcecategory_id', $this->resourcecategory_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
		$columns[] = 'unit_price';
		$columns[] = 'maximum';
		
		return $columns;
	}

}

?>