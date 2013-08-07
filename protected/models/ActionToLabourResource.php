<?php

/**
 * This is the model class for table "tbl_action_to_labour_resource".
 *
 * The followings are the available columns in table 'tbl_action_to_labour_resource':
 * @property integer $id
 * @property string $action_id
 * @property integer $labour_resource_id
 * @property integer $mode_id
 * @property string $level
 * @property integer $quantity
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Action $action
 * @property LabourResource $labourResource
 * @property User $updatedBy
 * @property Mode $mode
 * @property Level $level
 * @property ActionToLabourResourceBranch[] $actionToLabourResourceBranches
 * @property ActionToLabourResourceBranch[] $actionToLabourResourceBranches1
 * @property LabourResourceData[] $labourResourceDatas
 * @property TaskTemplateToActionToLabourResource[] $taskTemplateToActionToLabourResources
 */
class ActionToLabourResource extends ActiveRecord
{
	public $searchLabourResource;
	public $searchLevel;
	public $searchMode;

	/*
	 * these just here for purpose of tabs - ensuring these variables exist ensures than can be added to the url from currrent $_GET
	 */
	public $client_id;
	public $project_template_id;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'action' => array(self::BELONGS_TO, 'Action', 'action_id'),
            'labourResource' => array(self::BELONGS_TO, 'LabourResource', 'labour_resource_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'level' => array(self::BELONGS_TO, 'Level', 'level'),
            'actionToLabourResourceBranches' => array(self::HAS_MANY, 'ActionToLabourResourceBranch', 'action_id'),
            'actionToLabourResourceBranches1' => array(self::HAS_MANY, 'ActionToLabourResourceBranch', 'action_to_labour_resource_id'),
            'labourResourceDatas' => array(self::HAS_MANY, 'LabourResourceData', 'action_to_labour_resource_id'),
            'taskTemplateToActionToLabourResources' => array(self::HAS_MANY, 'TaskTemplateToActionToLabourResource', 'action_to_labour_resource_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchLabourResource', $this->searchLabourResource, 'labourResource.auth_item_name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level.name', true);

		// with
		$criteria->with = array(
			'labourResource',
			'level',
			'mode',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchLabourResource';
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
			'searchLabourResource',
			'searchMode',
			'searchLevel',
		);
	}

	public static function addLabourResources($action_id, $task, $models)
	{
		// loop thru labour resources for the Action
		foreach(ActionToLabourResource::model()->findAllByAttributes(array('action_id'=>$action_id)) as $actionToLabourResource)
		{
			// create a new labourResource
			$taskToLabourResource = new TaskToLabourResource();
			$taskToLabourResource->attributes = $actionToLabourResource->attributes;
			$taskToLabourResource->action_to_labour_resource_id = $actionToLabourResource->id;
			// if there is an associated task_template
			if($taskTemplateToActionToLabourResource = TaskTemplateToActionToLabourResource::model()->findByAttributes(array(
				'task_template_id'=>$task->taskTemplate->id,
				'action_to_labour_resource_id'=>$actionToLabourResource->id)))
			{
				$taskToLabourResource->attributes = $taskTemplateToActionToLabourResource->attributes;
			}
			$taskToLabourResource->updated_by = null;
			$taskToLabourResource->task_id = $task->id;
			$taskToLabourResource->id = null;
			$taskToLabourResource->createSave($models);
		}

		// not interested in failed duplicates
		return true;
	}

}