<?php

/**
 * This is the model class for table "tbl_supplier".
 *
 * The followings are the available columns in table 'tbl_supplier':
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property PurchaseOrder[] $purchaseOrders
 * @property ResourceToSupplier[] $resourceToSuppliers
 * @property User $updatedBy
 * @property SupplierToSupplierContact[] $supplierToSupplierContacts
 */
class Supplier extends ActiveRecord
{

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
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
            'purchaseOrders' => array(self::HAS_MANY, 'PurchaseOrder', 'supplier_id'),
            'resourceToSuppliers' => array(self::HAS_MANY, 'ResourceToSupplier', 'supplier_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'supplierToSupplierContacts' => array(self::HAS_MANY, 'SupplierToSupplierContact', 'supplier_id'),
        );
    }


	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);

		$criteria->select=array(
			't.id',
			't.name',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = $this->linkThisColumn('name');

		return $columns;
	}

}

?>