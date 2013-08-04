<?php

/**
 * This is the model class for table "tbl_task_template_to_human_resource".
 *
 * The followings are the available columns in table 'tbl_task_template_to_human_resource':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $human_resource_id
 * @property string $level
 * @property integer $mode_id
 * @property integer $quantity
 * @property string $duration
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplateToExclusiveRole[] $taskTemplateToExclusiveRoles
 * @property TaskTemplateToExclusiveRole[] $taskTemplateToExclusiveRoles1
 * @property TaskTemplateToExclusiveRole[] $taskTemplateToExclusiveRoles2
 * @property TaskTemplate $taskTemplate
 * @property HumanResource $humanResource
 * @property User $updatedBy
 * @property Mode $mode
 * @property Level $level0
 */
class TaskTemplateToHumanResource extends ActiveRecord
{
	public $searchHumanResource;
	public $searchLevel;
	public $searchMode;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'taskTemplateToExclusiveRoles' => array(self::HAS_MANY, 'TaskTemplateToExclusiveRole', 'task_template_id'),
            'taskTemplateToExclusiveRoles1' => array(self::HAS_MANY, 'TaskTemplateToExclusiveRole', 'parent_id'),
            'taskTemplateToExclusiveRoles2' => array(self::HAS_MANY, 'TaskTemplateToExclusiveRole', 'child_id'),
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'humanResource' => array(self::BELONGS_TO, 'HumanResource', 'human_resource_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchHumanResource', $this->searchHumanResource, 'humanResource.auth_item_name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level0.name', true);

		// with
		$criteria->with = array(
			'humanResource',
			'level0',
			'mode',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchHumanResource', 'HumanResource', 'human_resource_id');
 		$columns[] = 'quantity';
		$columns[] = 'duration';
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
			'searchHumanResource',
			'searchMode',
			'searchLevel',
		);
	}

	public function scopeTaskTemplate($exclude_id, $task_template_id, $mode_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('t.task_template_id', $task_template_id);
		$criteria->compare('t.mode_id', $mode_id);
		$criteria->addNotInCondition('t.id', array($exclude_id));

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
}