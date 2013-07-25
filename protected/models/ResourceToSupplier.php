<?php

/**
 * This is the model class for table "tbl_resource_to_supplier".
 *
 * The followings are the available columns in table 'tbl_resource_to_supplier':
 * @property integer $id
 * @property integer $resource_id
 * @property integer $supplier_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ResourceData[] $resourceDatas
 * @property ResourceData[] $resourceDatas1
 * @property Resource $resource
 * @property Supplier $supplier
 * @property User $updatedBy
 */
class ResourceToSupplier extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Supplier';
	
	public function scopeResource($resourceId)
	{
		// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
		$criteria=new DbCriteria;
		$criteria->compare('t.resource_id', $resourceId);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('resource_id, supplier_id', 'required'),
			array('resource_id, supplier_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, searchSupplier, resource_id, supplier_id,', 'safe', 'on'=>'search'),
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
            'resourceDatas' => array(self::HAS_MANY, 'ResourceData', 'resource_id'),
            'resourceDatas1' => array(self::HAS_MANY, 'ResourceData', 'resource_to_supplier_id'),
            'resource' => array(self::BELONGS_TO, 'Resource', 'resource_id'),
            'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'resource_id' => 'Resource Type',
			'supplier_id' => 'Supplier',
			'searchSupplier' => 'Supplier',
		));
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
			't.id',	// needed for delete and update buttons
			'supplier.name AS searchSupplier',
		);

		// where
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('supplier.name',$this->searchSupplier);

		// with
		$criteria->with = array(
			'supplier',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchSupplier', 'Supplier', 'supplier_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchSupplier',
		);
	}

}