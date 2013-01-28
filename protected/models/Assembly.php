<?php

/**
 * This is the model class for table "assembly".
 *
 * The followings are the available columns in table 'assembly':
 * @property integer $id
 * @property integer $store_id
 * @property string $description
 * @property string $alias
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property Store $store
 * @property Assembly $parent
 * @property Assembly[] $assemblies
 * @property AssemblyToAssembly[] $assemblyToAssemblies
 * @property AssemblyToAssembly[] $assemblyToAssemblies1
 * @property AssemblyToAssembly[] $assemblyToAssemblies2
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
			array('description, store_id, staff_id', 'required'),
			array('deleted, store_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('description, alias', 'length', 'max'=>255),
			array('id, description, store_id, alias, searchStaff', 'safe', 'on'=>'search'),
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
			'store' => array(self::BELONGS_TO, 'Store', 'store_id'),
			'assemblyToAssemblies' => array(self::HAS_MANY, 'AssemblyToAssembly', 'parent_assembly_id'),
			'assemblyToAssemblies1' => array(self::HAS_MANY, 'AssemblyToAssembly', 'store_id'),
			'assemblyToAssemblies2' => array(self::HAS_MANY, 'AssemblyToAssembly', 'child_assembly_id'),
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
			'store_id' => 'Store',
			'searchStore' => 'Store',
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
		$criteria->compare('t.alias',$this->alias,true);
		$criteria->compare('t.store_id',$this->store_id);

		$criteria->select=array(
			't.id',
			't.description',
			't.alias',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		// link to admin displaying children or if no children then just description without link
        $columns[] = $this->linkColumnAdjacencyList('description');
		$columns[] = 'alias';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'description',
			'alias',
		);
	}
 
	public function getSearchSort()
	{
		return array('searchStore');
	}

	public function scopeStore($store_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('store_id', $store_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}

?>