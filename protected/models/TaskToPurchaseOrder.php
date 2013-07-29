<?php

/**
 * This is the model class for table "tbl_task_to_purchase_order".
 *
 * The followings are the available columns in table 'tbl_task_to_purchase_order':
 * @property string $id
 * @property string $task_id
 * @property string $purchase_order_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property PurchaseOrder $purchaseOrder
 * @property User $updatedBy
 */
class TaskToPurchaseOrder extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	public $searchPurchaseOrder;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Purhase order';

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
            'purchaseOrder' => array(self::BELONGS_TO, 'PurchaseOrder', 'purchase_order_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'purchase_order_id' => 'Supplier/Purchase order',
			'searchPurchaseOrder' => 'Purchase order',
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
			't.purchase_order_id',
			"CONCAT_WS('$delimiter',
				supplier.name,
				purchaseOrder.number
				) AS searchPurchaseOrder",
		);

		// where
		$this->compositeCriteria($criteria,
			array(
			'supplier.name',
			'purchaseOrder.number'
			),
			$this->searchPurchaseOrder
		);
		$criteria->compare('t.task_id',$this->task_id);
		
		// with
		$criteria->with = array(
			'purchaseOrder',
			'purchaseOrder.supplier',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchPurchaseOrder', 'PurchaseOrder', 'purchase_order_id');
		
		return $columns;
	}

	
	static function getDisplayAttr()
	{
		return array(
			'searchPurchaseOrder',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'searchPurchaseOrder');
	}
}