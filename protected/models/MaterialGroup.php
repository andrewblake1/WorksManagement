<?php

/**
 * This is the model class for table "material_group".
 *
 * The followings are the available columns in table 'material_group':
 * @property integer $id
 * @property string $description
 * @property integer $store_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups1
 * @property Staff $staff
 * @property Store $store
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials1
 */
class MaterialGroup extends ActiveRecord
{
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, store_id', 'required'),
			array('store_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, store_id', 'safe', 'on'=>'search'),
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
			'assemblyToMaterialGroups' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'store_id'),
			'assemblyToMaterialGroups1' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'material_group_id'),
			'store' => array(self::BELONGS_TO, 'Store', 'store_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'materialGroupToMaterials' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'store_id'),
			'materialGroupToMaterials1' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'material_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'store_id' => 'Store',
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
		$criteria->compare('t.store_id',$this->store_id);

		$criteria->select=array(
			't.id',
			't.description',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = $this->linkThisColumn('description');
		
		return $columns;
	}

	public function scopeStore($store_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('store_id', $store_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}

?>