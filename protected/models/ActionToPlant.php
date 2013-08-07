<?php

/**
 * This is the model class for table "tbl_action_to_plant".
 *
 * The followings are the available columns in table 'tbl_action_to_plant':
 * @property integer $id
 * @property string $action_id
 * @property integer $plant_id
 * @property integer $mode_id
 * @property string $level
 * @property integer $quantity
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Action $action
 * @property Plant $plant
 * @property User $updatedBy
 * @property Mode $mode
 * @property Level $level
 * @property ActionToPlantBranch[] $actionToPlantBranches
 * @property ActionToPlantBranch[] $actionToPlantBranches1
 * @property PlantData[] $plantDatas
 * @property TaskTemplateToActionToPlant[] $taskTemplateToActionToPlants
 */
class ActionToPlant extends ActiveRecord
{
	public $searchPlant;
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
            'plant' => array(self::BELONGS_TO, 'Plant', 'plant_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'level' => array(self::BELONGS_TO, 'Level', 'level'),
            'actionToPlantBranches' => array(self::HAS_MANY, 'ActionToPlantBranch', 'action_id'),
            'actionToPlantBranches1' => array(self::HAS_MANY, 'ActionToPlantBranch', 'action_to_plant_id'),
            'plantDatas' => array(self::HAS_MANY, 'PlantData', 'action_to_plant_id'),
            'taskTemplateToActionToPlants' => array(self::HAS_MANY, 'TaskTemplateToActionToPlant', 'action_to_plant_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchPlant', $this->searchPlant, 'plant.auth_item_name', true);
		$criteria->compareAs('searchMode', $this->searchMode, 'mode.description', true);
		$criteria->compareAs('searchLevel', $this->searchLevel, 'level.name', true);

		// with
		$criteria->with = array(
			'plant',
			'level',
			'mode',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchPlant';
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
			'searchPlant',
			'searchMode',
			'searchLevel',
		);
	}

	public static function addPlants($action_id, $task, $models)
	{
		// loop thru plant for the Action
		foreach(ActionToPlant::model()->findAllByAttributes(array('action_id'=>$action_id)) as $actionToPlant)
		{
			// create a new plant
			$taskToPlant = new TaskToPlant();
			$taskToPlant->attributes = $actionToPlant->attributes;
			$taskToPlant->action_to_plant_id = $actionToPlant->id;
			// if there is an associated task_template
			if($taskTemplateToActionToPlant = TaskTemplateToActionToPlant::model()->findByAttributes(array(
				'task_template_id'=>$task->taskTemplate->id,
				'action_to_plant_id'=>$actionToPlant->id)))
			{
				$taskToPlant->attributes = $taskTemplateToActionToPlant->attributes;
			}
			$taskToPlant->updated_by = null;
			$taskToPlant->task_id = $task->id;
			$taskToPlant->id = null;
			$taskToPlant->createSave($models);
		}

		// not interested in failed duplicates
		return true;
	}

}