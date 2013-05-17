<?php

/**
 * This is the model class for table "tbl_purchase_order".
 *
 * The followings are the available columns in table 'tbl_purchase_order':
 * @property string $id
 * @property integer $supplier_id
 * @property string $number
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Supplier $supplier
 * @property User $updatedBy
 * @property TaskToPurchaseOrder[] $taskToPurchaseOrders
 */
class PurchaseOrder extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchSupplier;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('supplier_id, number', 'required'),
			array('supplier_id', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchSupplier, number', 'safe', 'on'=>'search'),
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
            'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskToPurchaseOrders' => array(self::HAS_MANY, 'TaskToPurchaseOrder', 'purchase_order_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'supplier_id' => 'Supplier',
			'searchSupplier' => 'Supplier',
			'number' => 'Number',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('supplier.name',$this->searchSupplier, true);
		$criteria->compare('t.number',$this->number,true);
		
		$criteria->with = array('supplier');

		$criteria->select=array(
			't.id',
			't.supplier_id',
			'supplier.name AS searchSupplier',
			't.number',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
        $columns[] = static::linkColumn('searchSupplier', 'Supplier', 'supplier_id');
		$columns[] = $this->linkThisColumn('number');
		
		return $columns;
	}
	
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'supplier->name',
			'number',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchSupplier');
	}
}

?>