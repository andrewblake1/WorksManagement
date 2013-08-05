<?php

/**
 * This is the model class for table "tbl_action_to_human_resource".
 *
 * The followings are the available columns in table 'tbl_action_to_human_resource':
 * @property integer $id
 * @property string $action_id
 * @property integer $human_resource_id
 * @property integer $mode_id
 * @property string $level
 * @property integer $quantity
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Action $action
 * @property HumanResource $humanResource
 * @property User $updatedBy
 * @property Mode $mode
 * @property Level $level
 * @property ActionToHumanResourceBranch[] $actionToHumanResourceBranches
 * @property ActionToHumanResourceBranch[] $actionToHumanResourceBranches1
 * @property HumanResourceData[] $humanResourceDatas
 * @property TaskTemplateToActionToHumanResource[] $taskTemplateToActionToHumanResources
 */
class ActionToHumanResource extends ActiveRecord
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
            'action' => array(self::BELONGS_TO, 'Action', 'action_id'),
            'humanResource' => array(self::BELONGS_TO, 'HumanResource', 'human_resource_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'level' => array(self::BELONGS_TO, 'Level', 'level'),
            'actionToHumanResourceBranches' => array(self::HAS_MANY, 'ActionToHumanResourceBranch', 'action_id'),
            'actionToHumanResourceBranches1' => array(self::HAS_MANY, 'ActionToHumanResourceBranch', 'action_to_human_resource_id'),
            'humanResourceDatas' => array(self::HAS_MANY, 'HumanResourceData', 'action_to_human_resource_id'),
            'taskTemplateToActionToHumanResources' => array(self::HAS_MANY, 'TaskTemplateToActionToHumanResource', 'action_to_human_resource_id'),
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
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level.name', true);

		// with
		$criteria->with = array(
			'humanResource',
			'level',
			'mode',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchHumanResource';
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

	public static function addHumanResources($action_id, $task, $models)
	{
		// loop thru human resources for the Action
		foreach(ActionToHumanResource::model()->findAllByAttributes(array('action_id'=>$action_id)) as $actionToHumanResource)
		{
			// create a new humanResource
			$taskToHumanResource = new TaskToHumanResource();
			$taskToHumanResource->attributes = $actionToHumanResource->attributes;
			$taskToHumanResource->action_to_human_resource_id = $actionToHumanResource->id;
			// if there is an associated task_template
			if($taskTemplateToActionToHumanResource = TaskTemplateToActionToHumanResource::model()->findByAttributes(array(
				'task_template_id'=>$task->taskTemplate->id,
				'action_to_human_resource_id'=>$actionToHumanResource->id)))
			{
				$taskToHumanResource->attributes = $taskTemplateToActionToHumanResource->attributes;
			}
			$taskToHumanResource->updated_by = null;
			$taskToHumanResource->task_id = $task->id;
			$taskToHumanResource->id = null;
			$taskToHumanResource->createSave($models);
		}

		// not interested in failed duplicates
		return true;
	}

}