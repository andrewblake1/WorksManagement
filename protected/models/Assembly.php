<?php

/**
 * This is the model class for table "assembly".
 *
 * The followings are the available columns in table 'assembly':
 * @property integer $id
 * @property integer $supplier_id
 * @property string $description
 * @property string $unit_price
 * @property string $alias
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property Supplier $supplier
 * @property AssemblyToClient[] $assemblyToClients
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToStandardDrawing[] $assemblyToStandardDrawings
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskTypeToAssembly[] $taskTypeToAssemblies
 */
class Assembly extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assembly';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, supplier_id, staff_id', 'required'),
			array('deleted, supplier_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('description, alias', 'length', 'max'=>255),
			array('unit_price', 'length', 'max'=>7),
			array('id, description, unit_price, supplier_id, alias, searchStaff', 'safe', 'on'=>'search'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'supplier' => array(self::BELONGS_TO, 'Supplier', 'supplier_id'),
			'assemblyToClients' => array(self::HAS_MANY, 'AssemblyToClient', 'assembly_id'),
			'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'assembly_id'),
			'assemblyToStandardDrawings' => array(self::HAS_MANY, 'AssemblyToStandardDrawing', 'assembly_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'assembly_id'),
			'taskTypeToAssemblies' => array(self::HAS_MANY, 'TaskTypeToAssembly', 'assembly_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Assembly',
			'unit_price' => 'Unit price',
			'supplier_id' => 'Supplier',
			'searchSupplier' => 'Supplier',
			'alias' => 'Alias',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.unit_price',$this->unit_price,true);
		$criteria->compare('t.supplier_id', $this->supplier_id);

		$criteria->select=array(
			't.id',
			't.description',
			't.unit_price',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
		$columns[] = 'unit_price';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'description',
		);
	}
 
	public function getSearchSort()
	{
		return array('searchSupplier');
	}

	public function scopeSupplier($supplier_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('supplier_id', $supplier_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}

?>