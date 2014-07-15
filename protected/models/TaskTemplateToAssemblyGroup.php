<?php

/**
 * This is the model class for table "tbl_task_template_to_assembly_group".
 *
 * The followings are the available columns in table 'tbl_task_template_to_assembly_group':
 * @property string $id
 * @property integer $task_template_id
 * @property integer $assembly_group_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property string $selection_tooltip
 * @property string $comment
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property AssemblyGroup $assemblyGroup
 * @property User $updatedBy
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups1
 */
class TaskTemplateToAssemblyGroup extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssemblyGroup;
	public $standard_id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('standard_id, quantity', 'required'),
			array('standard_id', 'numerical', 'integerOnly'=>true),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroup', 'assembly_group_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'assembly_group_id'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups1' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'task_template_to_assembly_group_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchAssemblyGroup', $this->searchAssemblyGroup, 'assemblyGroup.description', true);
		
		$criteria->with = array(
			'assemblyGroup',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchAssemblyGroup';
 		$columns[] = 'quantity';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'select';
 		$columns[] = 'quantity_tooltip';
 		$columns[] = 'selection_tooltip';
 		$columns[] = 'comment';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchAssemblyGroup',
			'comment',
		);
	}
	
	public function afterFind() {
		$assemblyGroup = AssemblyGroup::model()->findByPk($this->assembly_group_id);
		$this->standard_id = $assemblyGroup->standard_id;
		
		parent::afterFind();
	}
	
}