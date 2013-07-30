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
 * @property TaskTemplate $taskTemplate
 * @property TaskTemplate $projectTemplate
 * @property User $updatedBy
 * @property Action $action
 * @property TaskTemplate $client
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
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'projectTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'project_template_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'action' => array(self::BELONGS_TO, 'Action', 'action_id'),
            'client' => array(self::BELONGS_TO, 'TaskTemplate', 'client_id'),
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
$t = $this->attributes;		
		return parent::save();
	}

}

?>