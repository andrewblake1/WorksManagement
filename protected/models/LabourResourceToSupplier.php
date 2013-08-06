<?php

/**
 * This is the model class for table "tbl_labour_resource_to_supplier".
 *
 * The followings are the available columns in table 'tbl_labour_resource_to_supplier':
 * @property integer $id
 * @property integer $labour_resource_id
 * @property integer $supplier_id
 * @property string $unit_price
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property LabourResourceData[] $labourResourceDatas
 * @property LabourResourceData[] $labourResourceDatas1
 * @property LabourResource $labourResource
 * @property Supplier $supplier
 * @property User $updatedBy
 */
class LabourResourceToSupplier extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;
	
	public function scopeLabourResource($labourResourceId)
	{
		// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
		$criteria=new DbCriteria;
		$criteria->compare('t.labour_resource_id', $labourResourceId);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'resourceDatas' => array(self::HAS_MANY, 'LabourResourceData', 'labour_resource_id'),
            'resourceDatas1' => array(self::HAS_MANY, 'LabourResourceData', 'labour_resource_to_supplier_id'),
            'labourResource' => array(self::BELONGS_TO, 'LabourResource', 'labour_resource_id'),
            'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchSupplier', $this->searchSupplier, 'supplier.name', true);

		// with
		$criteria->with = array(
			'supplier',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchSupplier', 'Supplier', 'supplier_id');
        $columns[] = 'unit_price';
		
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