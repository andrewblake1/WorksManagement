<?php

/**
 * This is the model class for table "tbl_material".
 *
 * The followings are the available columns in table 'tbl_material':
 * @property integer $id
 * @property integer $standard_id
 * @property string $description
 * @property string $unit
 * @property string $alias
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterial[] $assemblyToMaterials1
 * @property User $updatedBy
 * @property Standard $standard
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials
 * @property MaterialToClient[] $materialToClients
 * @property TaskTemplateToMaterial[] $taskTemplateToMaterials
 * @property TaskToMaterial[] $taskToMaterials
 */
class Material extends ActiveRecord
{

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, standard_id', 'required'),
			array('standard_id', 'numerical', 'integerOnly'=>true),
			array('description, alias', 'length', 'max'=>255),
			array('unit', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, standard_id, description, unit, alias', 'safe', 'on'=>'search'),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'standard' => array(self::BELONGS_TO, 'Standard', 'standard_id'),
            'materialGroupToMaterials' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'material_id'),
            'materialToClients' => array(self::HAS_MANY, 'MaterialToClient', 'material_id'),
            'taskTemplateToMaterials' => array(self::HAS_MANY, 'TaskTemplateToMaterial', 'material_id'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'material_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'unit' => 'Unit',
			'standard_id' => 'Standard',
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
		$criteria->compare('t.standard_id', $this->standard_id);

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
		return array('searchStandard');
	}

	public function scopeStandard($standard_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('standard_id', $standard_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	public function scopeMaterialGroup($material_group_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('materialGroupToMaterial.material_group_id', $material_group_id);

		// join
		$criteria->join = '
			JOIN tbl_material_group_to_material materialGroupToMaterial ON materialGroupToMaterial.material_id = t.id
		';

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

/*	public function scopeAssembly($assembly_id)
	{
		$assembly = Assembly::model()->findByPk($assembly_id);
		
		return $this->scopeStandard($assembly->standard_id);
	}

	public function scopeMaterialGroup($material_group_id)
	{
		$assembly = MaterialGroup::model()->findByPk($assembly_id);
		
		return $this->scopeStandard($assembly->standard_id);
	}
*/
}

?>