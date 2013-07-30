<?php

/**
 * This is the model class for table "tbl_assembly_group_to_assembly".
 *
 * The followings are the available columns in table 'tbl_assembly_group_to_assembly':
 * @property string $id
 * @property integer $assembly_group_id
 * @property integer $assembly_id
 * @property integer $standard_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property AssemblyGroup $assemblyGroup
 * @property Assembly $standard
 * @property Assembly $assembly
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups1
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups2
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups1
 */
class AssemblyGroupToAssembly extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssembly;
	public $searchAlias;
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroup', 'assembly_group_id'),
            'standard' => array(self::BELONGS_TO, 'Assembly', 'standard_id'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'assembly_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups1' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'assembly_group_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups2' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'assembly_group_to_assembly_id'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'assembly_id'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups1' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'assembly_group_to_assembly_id'),
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
			't.id',	// needed for delete and update buttons
			'assembly.description AS searchAssembly',
			'assembly.alias AS searchAlias',
		);

		// where
		$criteria->compare('assembly_group_id',$this->assembly_group_id);
		$criteria->compare('assembly.description',$this->searchAssembly,true);
		$criteria->compare('assembly.alias',$this->searchAlias,true);

		// with
		$criteria->with = array(
			'assembly',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchAssembly';
 		$columns[] = 'searchAlias';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchAssembly',
			'searchAlias',
		);
	}

	public function beforeValidate()
	{
		$assemblyGroup = AssemblyGroup::model()->findByPk($this->assembly_group_id);
		$this->standard_id = $assemblyGroup->standard_id;
		
		return parent::beforeValidate();
	}

}