<?php

/**
 * This is the model class for table "task_to_purchase_order".
 *
 * The followings are the available columns in table 'task_to_purchase_order':
 * @property string $id
 * @property string $task_id
 * @property string $purchase_order_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property PurchaseOrder $purchaseOrder
 * @property Staff $staff
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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_to_purchase_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, purchase_order_id, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id, purchase_order_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, searchTask, searchPurchaseOrder, staff_id', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'purchaseOrder' => array(self::BELONGS_TO, 'PurchaseOrder', 'purchase_order_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Task to purchase order',
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'purchase_order_id' => 'Supplier/Purchase order',
			'searchPurchaseOrder' => 'Purchase order',
		);
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
		
		// join
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
			'purchaseOrder->supplier->name',
			'purchaseOrder->number',
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