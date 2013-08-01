<?php

/**
 * This is the model class for table "tbl_task_template_to_role".
 *
 * The followings are the available columns in table 'tbl_task_template_to_role':
 * @property integer $id
 * @property integer $task_template_id
 * @property string $auth_item_name
 * @property string $level
 * @property integer $mode_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplateToExclusiveRole[] $taskTemplateToExclusiveRoles
 * @property TaskTemplateToExclusiveRole[] $taskTemplateToExclusiveRoles1
 * @property TaskTemplateToExclusiveRole[] $taskTemplateToExclusiveRoles2
 * @property TaskTemplate $taskTemplate
 * @property User $updatedBy
 * @property Level $level0
 * @property Mode $mode
 * @property AuthItem $authItemName
 */
class TaskTemplateToRole extends ActiveRecord
{
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
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
			'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
			'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'auth_item_name'),
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level0.name', true);

		// with
		$criteria->with = array(
			'level0',
			'mode',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'auth_item_name';
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
			'auth_item_name',
		);
	}

}