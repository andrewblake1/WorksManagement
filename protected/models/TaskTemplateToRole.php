<?php

/**
 * This is the model class for table "tbl_task_template_to_role".
 *
 * The followings are the available columns in table 'tbl_task_template_to_role':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $human_resource_id
 * @property string $level
 * @property integer $mode_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplateToExclusiveRole[] $tblTaskTemplateToExclusiveRoles
 * @property TaskTemplateToExclusiveRole[] $tblTaskTemplateToExclusiveRoles1
 * @property TaskTemplateToExclusiveRole[] $tblTaskTemplateToExclusiveRoles2
 * @property TaskTemplate $taskTemplate
 * @property User $updatedBy
 * @property Mode $mode
 * @property Level $level0
 * @property HumanResource $humanResource
 */
class TaskTemplateToRole extends ActiveRecord
{
	public $searchLevel;
	public $searchMode;
	public $searchRole;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Additional HR Role';
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'tblTaskTemplateToExclusiveRoles' => array(self::HAS_MANY, 'TaskTemplateToExclusiveRole', 'task_template_id'),
            'tblTaskTemplateToExclusiveRoles1' => array(self::HAS_MANY, 'TaskTemplateToExclusiveRole', 'parent_id'),
            'tblTaskTemplateToExclusiveRoles2' => array(self::HAS_MANY, 'TaskTemplateToExclusiveRole', 'child_id'),
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
            'humanResource' => array(self::BELONGS_TO, 'HumanResource', 'human_resource_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchRole', $this->searchRole, 'humanResource.auth_item_name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level0.name', true);

		// with
		$criteria->with = array(
			'level0',
			'mode',
			'humanResource',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchRole';
		$columns[] = 'searchMode';
		$columns[] = 'searchLevel';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchRole',
		);
	}

	public function scopeTaskTemplate($exclude_id, $task_template_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('t.task_template_id', $task_template_id);
		$criteria->addNotInCondition('t.id', array($exclude_id));

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
}