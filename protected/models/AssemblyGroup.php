<?php

/**
 * This is the model class for table "tbl_assembly_group".
 *
 * The followings are the available columns in table 'tbl_assembly_group':
 * @property integer $id
 * @property string $description
 * @property integer $standard_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property Standard $standard
 * @property AssemblyGroupToAssembly[] $assemblyGroupToAssemblies
 * @property AssemblyToAssemblyGroup[] $assemblyToAssemblyGroups
 * @property AssemblyToAssemblyGroup[] $assemblyToAssemblyGroups1
 * @property TaskTemplateToAssemblyGroup[] $taskTemplateToAssemblyGroups
 */
class AssemblyGroup extends ActiveRecord
{
	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'standard' => array(self::BELONGS_TO, 'Standard', 'standard_id'),
            'assemblyGroupToAssemblies' => array(self::HAS_MANY, 'AssemblyGroupToAssembly', 'assembly_group_id'),
            'assemblyToAssemblyGroups' => array(self::HAS_MANY, 'AssemblyToAssemblyGroup', 'standard_id'),
            'assemblyToAssemblyGroups1' => array(self::HAS_MANY, 'AssemblyToAssemblyGroup', 'assembly_group_id'),
            'taskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskTemplateToAssemblyGroup', 'assembly_group_id'),
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