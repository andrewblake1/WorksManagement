<?php

/**
 * This is the model class for table "purchase_orders".
 *
 * The followings are the available columns in table 'purchase_orders':
 * @property string $id
 * @property integer $supplier_id
 * @property string $purchase_order_no
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Supplier $supplier
 * @property Staff $staff
 * @property Task[] $tasks
 */
class PurchaseOrders extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PurchaseOrders the static model class
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
		return 'purchase_orders';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('supplier_id, purchase_order_no, staff_id', 'required'),
			array('supplier_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('purchase_order_no', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, supplier_id, purchase_order_no, staff_id', 'safe', 'on'=>'search'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'purchase_orders_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'supplier_id' => 'Supplier',
			'purchase_order_no' => 'Purchase Order No',
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
		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('purchase_order_no',$this->purchase_order_no,true);
		$criteria->compare('staff_id',$this->staff_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}