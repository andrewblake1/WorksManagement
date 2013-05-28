<?php

/**
 * This is the model class for table "tbl_action".
 *
 * The followings are the available columns in table 'tbl_action':
 * @property string $id
 * @property integer $client_id
 * @property integer $project_template_id
 * @property string $description
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property Client $client
 * @property ProjectTemplate $projectTemplate
 * @property DutyStep[] $dutySteps
 * @property TaskTemplateToAction[] $taskTemplateToActions
 */
class Action extends ActiveRecord
{
	public function scopeTaskTemplate($taskTemplateId)
	{
		$taskTemplate = TaskTemplate::model()->findByPk($taskTemplateId);
		
		// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
		$criteria=new DbCriteria;
		$criteria->compare('t.project_template_id', $taskTemplate->project_template_id);
		$criteria->addCondition('project_template_id IS NULL', 'OR');

		$criteria2=new DbCriteria;
		$criteria2->compare('t.client_id', $taskTemplate->client_id);
		$criteria2->addCondition('client_id IS NULL', 'OR');

		$criteria->mergeWith($criteria2, 'AND');

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
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
			array('client_id, project_template_id', 'safe'),
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
            'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'action_id'),
            'taskTemplateToActions' => array(self::HAS_MANY, 'TaskTemplateToAction', 'action_id'),
        );
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
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.client_id',$this->client_id);
		$criteria->compare('t.project_template_id',$this->project_template_id);
		$criteria->compare('t.description',$this->description,true);

		// with
		$criteria->with=array(
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]='description';
		
		return $columns;
	}

}