<?php

/**
 * This is the model class for table "tbl_resource".
 *
 * The followings are the available columns in table 'tbl_resource':
 * @property integer $id
 * @property string $description
 * @property string $unit_price
 * @property integer $resource_category_id
 * @property integer $maximum
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ResourceCategory $resourceCategory
 * @property User $updatedBy
 * @property ResourceToSupplier[] $resourceToSuppliers
 * @property TaskTemplateToResource[] $taskTemplateToResources
 */
class Resource extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description', 'required'),
			array('resource_category_id, maximum', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('unit_price', 'length', 'max'=>7),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, description, unit_price, resource_category_id, maximum', 'safe', 'on'=>'search'),
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
            'resourceCategory' => array(self::BELONGS_TO, 'ResourceCategory', 'resource_category_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'resourceToSuppliers' => array(self::HAS_MANY, 'ResourceToSupplier', 'resource_id'),
            'taskTemplateToResources' => array(self::HAS_MANY, 'TaskTemplateToResource', 'resource_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'resource_category_id' => 'Resource category',
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
		$criteria->compare('t.resource_category_id', $this->resource_category_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'unit_price';
		$columns[] = 'maximum';
		
		return $columns;
	}

}

?>