<?php

/**
 * This is the model class for table "tbl_task_template_to_action_to_labour_resource".
 *
 * The followings are the available columns in table 'tbl_task_template_to_action_to_labour_resource':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $action_to_labour_resource_id
 * @property integer $task_template_to_action_id
 * @property integer $quantity
 * @property string $duration
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property ActionToLabourResource $actionToLabourResource
 * @property User $updatedBy
 */
class TaskTemplateToActionToLabourResource extends ActiveRecord
{
	public $searchLabourResource;
	public $searchMode;
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
			'actionToLabourResource' => array(self::BELONGS_TO, 'ActionToLabourResource', 'action_to_labour_resource_id'),
            'taskTemplateToAction' => array(self::BELONGS_TO, 'TaskTemplateToAction', 'task_template_to_action_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchLabourResource', $this->searchLabourResource, 'labourResource.auth_item_name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);

		$criteria->with=array(
			'actionToLabourResource.mode',
			'actionToLabourResource.labourResource',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchLabourResource';
  		$columns[] = 'searchMode';
        $columns[] = 'quantity';
        $columns[] = 'duration';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchLabourResource',
		);
	}
 	
	public function beforeValidate()
	{
		$this->task_template_id = $this->taskTemplateToAction->task_template_id;
		
		return parent::beforeValidate();
	}
}