<?php

/**
 * This is the model class for table "tbl_action".
 *
 * The followings are the available columns in table 'tbl_action':
 * @property string $id
 * @property integer $client_id
 * @property integer $project_template_id
 * @property string $override_id
 * @property string $description
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property Client $client
 * @property ProjectTemplate $projectTemplate
 * @property Action $override
 * @property Action[] $actions
 * @property DutyStep[] $dutySteps
 * @property TaskTemplateToAction[] $taskTemplateToActions
 */
class Action extends ActiveRecord
{
	public $searchOverride;
	
	// used with task and task template to limit actions
	public function scopeTaskTemplate($taskTemplateId)
	{
		$taskTemplate = TaskTemplate::model()->findByPk($taskTemplateId);
		$projectTemplate = ProjectTemplate::model()->findByPk($taskTemplate->project_template_id);
		
		// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
		$criteria=new DbCriteria;
		$criteria->compare('t.project_template_id', $projectTemplate->id);
		$criteria->addCondition('t.project_template_id IS NULL', 'OR');

		$criteria2=new DbCriteria;
		$criteria2->compare('t.client_id', $projectTemplate->client_id);
		$criteria2->addCondition('t.client_id IS NULL', 'OR');

		// this gives us a list which is basically correct but doesn't take into account the overrides
		$criteria->mergeWith($criteria2, 'AND');

		// take into account the overrides. Can be max 2 overrides - 1 at template level and the next at client level
		// start by joining result on override to id to obtain
		$criteria->join = '
			LEFT JOIN tbl_action override ON t.id = override.override_id
				AND (
					t.client_id = override.client_id
					OR t.project_template_id = override.project_template_id)
		';

		// and finally - exclude any records that have a child
		$criteria->addCondition('override.override_id IS NULL');
		
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	// used by override to limit to releveant higher level
	public function scopeClient($clientId)
	{
		// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
		$criteria=new DbCriteria;
		$criteria->addCondition('t.project_template_id IS NULL');

		$criteria2=new DbCriteria;
		$criteria2->compare('t.client_id', $clientId);
		$criteria2->addCondition('t.client_id IS NULL', 'OR');

		// this gives us a list which is basically correct but doesn't take into account the overrides
		$criteria->mergeWith($criteria2, 'AND');

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	// used by override to limit to higher level
	public function scopeProjectTemplate($projectTemplateId)
	{
		$projectTemplate = ProjectTemplate::model()->findByPk($projectTemplateId);
		
		return $this->scopeClient($projectTemplate->client_id);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description, updated_by', 'required'),
			array('deleted, updated_by', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('client_id, project_template_id, override_id', 'safe'),
		));
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
            'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
            'projectTemplate' => array(self::BELONGS_TO, 'ProjectTemplate', 'project_template_id'),
            'override' => array(self::BELONGS_TO, 'Action', 'override_id'),
            'actions' => array(self::HAS_MANY, 'Action', 'override_id'),
            'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'action_id'),
            'taskTemplateToActions' => array(self::HAS_MANY, 'TaskTemplateToAction', 'action_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'standard_id' => 'Standard',
			'drawing_id' => 'Drawing',
			'searchOverride' => 'Replace',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.description',
			't.client_id',
			't.project_template_id',
			'override.description AS searchOverride',
		);

		// where
		
		// need to only show at top level
		$criteria->compare('t.id',$this->id);
		$criteria->compareNull('t.client_id',$this->client_id);
		$criteria->compareNull('t.project_template_id',$this->project_template_id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('override.description',$this->searchOverride,true);

		// with
		$criteria->with=array(
			'override',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]='description';
		if($this->client_id || $this->project_template_id)
		{
			$columns[]='searchOverride';
		}
		
		return $columns;
	}

}