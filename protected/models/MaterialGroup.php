<?php

/**
 * This is the model class for table "tbl_material_group".
 *
 * The followings are the available columns in table 'tbl_material_group':
 * @property integer $id
 * @property string $description
 * @property integer $standard_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups1
 * @property User $updatedBy
 * @property Standard $standard
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials1
 * @property TaskTemplateToMaterialGroup[] $taskTemplateToMaterialGroups
 * @property TaskTemplateToMaterialGroup[] $taskTemplateToMaterialGroups1
 */
class MaterialGroup extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'assemblyToMaterialGroups' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'standard_id'),
            'assemblyToMaterialGroups1' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'material_group_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'standard' => array(self::BELONGS_TO, 'Standard', 'standard_id'),
            'materialGroupToMaterials' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'standard_id'),
            'materialGroupToMaterials1' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'material_group_id'),
            'taskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskTemplateToMaterialGroup', 'material_group_id'),
            'taskTemplateToMaterialGroups1' => array(self::HAS_MANY, 'TaskTemplateToMaterialGroup', 'standard_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'standard_id' => 'Standard',
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
		$criteria->compare('t.standard_id',$this->standard_id);

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

	public function scopeStandard($standard_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('standard_id', $standard_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

}

?>