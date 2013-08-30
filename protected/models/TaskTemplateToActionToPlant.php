<?php

/**
 * This is the model class for table "tbl_task_template_to_action_to_plant".
 *
 * The followings are the available columns in table 'tbl_task_template_to_action_to_plant':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $action_to_plant_id
 * @property integer $task_template_to_action_id
 * @property integer $quantity
 * @property string $duration
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskTemplate $taskTemplate
 * @property ActionToPlant $actionToPlant
 * @property User $updatedBy
 */
class TaskTemplateToActionToPlant extends ActiveRecord
{
	static $niceNamePlural = 'Plant';

	public $searchPlant;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'actionToPlant' => array(self::BELONGS_TO, 'ActionToPlant', 'action_to_plant_id'),
            'taskTemplateToAction' => array(self::BELONGS_TO, 'TaskTemplateToAction', 'task_template_to_action_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchPlant', $this->searchPlant, 'plant.description', true);

		$criteria->with=array(
			'actionToPlant.plant',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchPlant';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchPlant',
		);
	}
	
	public function beforeValidate()
	{
		$this->task_template_id = $this->taskTemplateToAction->task_template_id;
		
		return parent::beforeValidate();
	}
}