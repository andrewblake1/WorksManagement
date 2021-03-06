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
 * @property ActionToLabourResource[] $actionToLabourResources
 * @property ActionToPlant[] $actionToPlants
 * @property DutyStep[] $dutySteps
 * @property TaskTemplateToAction[] $taskTemplateToActions
 */
class Action extends ActiveRecord
{
	public $searchOverride;
	
	// used with task and task template to limit actions
	public function scopeTaskTemplate($taskTemplateId, $modeId=null)
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
			LEFT JOIN (SELECT * FROM `tbl_action` WHERE ((deleted=0) AND ((((project_template_id=2) OR (project_template_id IS NULL)) AND ((client_id=8) OR (client_id IS NULL))) ))) override2 ON t.id = override2.override_id
		';

		// and finally - exclude any records that have a child
		$criteria->addCondition('override2.override_id IS NULL');
		
		// if a mode has been passed then we also want to limit by this - no point showing actions not applicable to current task mode and
		// and also no where to divert to afterwards as action would have no visible duties - causes crash unless hack around that
		if($modeId)
		{
			// this will be working with tbl_task_to_action as t only
			$criteria->compare('tbl_duty_step_to_mode.mode_id', $modeId);
			
			$criteria->join .= '
				JOIN tbl_duty_step ON tbl_duty_step.action_id = t.id AND tbl_duty_step.deleted = 0
				JOIN tbl_duty_step_to_mode ON tbl_duty_step.id = tbl_duty_step_to_mode.duty_step_id
			';
	
			$criteria->distinct = true;
		}

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
            'actionToLabourResources' => array(self::HAS_MANY, 'ActionToLabourResource', 'action_id'),
            'actionToPlants' => array(self::HAS_MANY, 'ActionToPlant', 'action_id'),
            'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'action_id'),
            'taskTemplateToActions' => array(self::HAS_MANY, 'TaskTemplateToAction', 'action_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this, array('client_id', 'project_template_id'));

		// need to only show at top level
		$criteria->compareNull('t.client_id',$this->client_id);
		$criteria->compareNull('t.project_template_id',$this->project_template_id);
		$criteria->compareAs('searchOverride', $this->searchOverride, 'override.description',true);

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