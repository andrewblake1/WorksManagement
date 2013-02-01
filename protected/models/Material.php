<?php

/**
 * This is the model class for table "material".
 *
 * The followings are the available columns in table 'material':
 * @property integer $id
 * @property integer $store_id
 * @property string $description
 * @property string $unit
 * @property string $alias
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterial[] $assemblyToMaterials1
 * @property Staff $staff
 * @property Store $store
 * @property MaterialToClient[] $materialToClients
 * @property MaterialToTask[] $materialToTasks
 * @property TaskTypeToMaterial[] $taskTypeToMaterials
 */
class Material extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'material';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, store_id, staff_id', 'required'),
			array('store_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description, alias', 'length', 'max'=>255),
			array('unit', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, store_id, description, searchStaff, unit, alias', 'safe', 'on'=>'search'),
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
			'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'store_id'),
			'assemblyToMaterials1' => array(self::HAS_MANY, 'AssemblyToMaterial', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'store' => array(self::BELONGS_TO, 'Store', 'store_id'),
			'materialToClients' => array(self::HAS_MANY, 'MaterialToClient', 'material_id'),
			'materialToTasks' => array(self::HAS_MANY, 'MaterialToTask', 'material_id'),
			'taskTypeToMaterials' => array(self::HAS_MANY, 'TaskTypeToMaterial', 'material_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'unit' => 'Unit',
			'store_id' => 'Store',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.description', $this->description,true);
		$criteria->compare('t.alias', $this->alias,true);
		$criteria->compare('t.unit', $this->unit);
		$criteria->compare('t.store_id', $this->store_id);

		$criteria->select=array(
			't.id',
			't.description',
			't.alias',
			't.unit',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'alias';
		$columns[] = 'unit';
		
		return $columns;
	}
	
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'description',
			'alias',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchStore');
	}

	public function scopeStore($store_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('store_id', $store_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	public function scopeAssembly($assembly_id)
	{
		$assembly = Assembly::model()->findByPk($assembly_id);
		
		return $this->scopeStore($assembly->store_id);
	}

}

?>