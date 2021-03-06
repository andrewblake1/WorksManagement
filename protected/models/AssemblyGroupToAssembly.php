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
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups
 */
class AssemblyGroupToAssembly extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssembly;
	public $searchAlias;
	public $searchDrawing;
	public $searchDrawingId;
	
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
            'taskToAssemblyToTaskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'assembly_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		// select
		$criteria->compareAs('searchAssembly', $this->searchAssembly, 'assembly.description', true);
		$criteria->compareAs('searchAlias', $this->searchAlias, 'assembly.alias', true);
		$criteria->compareAs('searchDrawing', $this->searchDrawing, 'drawing.description', true);
		$criteria->select[] = 'drawing.id AS searchDrawingId';

		// with
		$criteria->with = array(
			'assembly',
			'assembly.drawing',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchAssembly';
 		$columns[] = 'searchAlias';
		$columns[] = static::linkColumn('searchDrawing', 'Drawing', 'searchDrawingId');
		
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