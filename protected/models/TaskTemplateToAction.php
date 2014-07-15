<?php

/**
 * This is the model class for table "tbl_task_template_to_action".
 *
 * The followings are the available columns in table 'tbl_task_template_to_action':
 * @property integer $id
 * @property integer $task_template_id
 * @property integer $project_template_id
 * @property integer $client_id
 * @property string $action_id
 * @property string $importance
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskTemplate $client
 * @property TaskTemplate $taskTemplate
 * @property Action $action
 * @property TaskTemplate $projectTemplate
 * @property TaskTemplateToActionToLabourResource[] $taskTemplateToActionToLabourResources
 * @property TaskTemplateToActionToLabourResource[] $taskTemplateToActionToLabourResources1
 * @property TaskTemplateToActionToPlant[] $taskTemplateToActionToPlants
 * @property TaskTemplateToActionToPlant[] $taskTemplateToActionToPlants1
 */
class TaskTemplateToAction extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAction;
	public $searchTaskTemplate;

	/**
	 * Importance. These are the emum values set by the importance Custom field within 
	 * the database
	 */
	const importanceStandard = 'Standard';
	const importanceOptional = 'Optional';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return parent::rules(array('client_id', 'project_template_id'));
	}

	/**
	 * Returns importance labels.
	 * @return array data importance - to match enum type in mysql workbench
	 */
	public static function getImportanceLabels()
	{
		return array(
			self::importanceStandard=>self::importanceStandard,
			self::importanceOptional=>self::importanceOptional,
		);
	}

	/**
	 * Returns data type column names.
	 * @return array data storage types - to match enum type in mysql workbench
	 */
	public static function getImportanceColumnNames()
	{
		return array(
			self::importanceStandard=>'importanceStandard',
			self::importanceOptional=>'importanceOptional',
		);
	}

	public function scopeTask($task_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('task.id',$task_id);
		$criteria->join='JOIN tbl_task task USING(task_template_id)';
		
		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'client' => array(self::BELONGS_TO, 'TaskTemplate', 'client_id'),
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'action' => array(self::BELONGS_TO, 'Action', 'action_id'),
            'projectTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'project_template_id'),
            'taskTemplateToActionToLabourResources' => array(self::HAS_MANY, 'TaskTemplateToActionToLabourResource', 'task_template_id'),
            'taskTemplateToActionToLabourResources1' => array(self::HAS_MANY, 'TaskTemplateToActionToLabourResource', 'task_template_to_action_id'),
            'taskTemplateToActionToPlants' => array(self::HAS_MANY, 'TaskTemplateToActionToPlant', 'task_template_id'),
            'taskTemplateToActionToPlants1' => array(self::HAS_MANY, 'TaskTemplateToActionToPlant', 'task_template_to_action_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchAction', $this->searchAction, 'action.description', TRUE);

		// with
		$criteria->with = array(
			'action',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchAction', 'Action', 'action_id');
		$columns[] = 'importance';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchAction',
		);
	}
	
	public function save() {
		if($taskTemplate = TaskTemplate::model()->findByPk($this->task_template_id))
		{
			$this->client_id = $taskTemplate->client_id;
			$this->project_template_id = $taskTemplate->project_template_id;
		}
		
		return parent::save();
	}
	
	/**
	 * Labour resources and plant should be added automatically - the users should only have and need ability
	 * to alter duration and quantity of these
	 * @param type $attributes
	 */
	public function insert($attributes = null)
	{
		$return = parent::insert($attributes);

		// loop thru and add labour resources
		foreach($this->action->actionToLabourResources as $actionToLabourResource)
		{
			$taskTemplateToActionToLabourResource = new TaskTemplateToActionToLabourResource;
			$taskTemplateToActionToLabourResource->task_template_id = $this->task_template_id;
			$taskTemplateToActionToLabourResource->action_to_labour_resource_id = $actionToLabourResource->id;
			$taskTemplateToActionToLabourResource->task_template_to_action_id = $this->id;
			$taskTemplateToActionToLabourResource->quantity = $actionToLabourResource->quantity;
			$taskTemplateToActionToLabourResource->insert();
		}

		// loop thru and add plant resources
		foreach($this->action->actionToPlants as $actionToPlant)
		{
			$taskTemplateToActionToPlant = new TaskTemplateToActionToPlant;
			$taskTemplateToActionToPlant->task_template_id = $$this->task_template_id;
			$taskTemplateToActionToPlant->action_to_plant_id = $$actionToPlant->id;
			$taskTemplateToActionToPlant->task_template_to_action_id = $$this->id;
			$taskTemplateToActionToPlant->quantity = $$actionToPlant->quantity;
			$taskTemplateToActionToPlant->insert();
		}

		return $return;
	}

}

?>