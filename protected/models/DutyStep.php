<?php

/**
 * This is the model class for table "tbl_duty_step".
 *
 * The followings are the available columns in table 'tbl_duty_step':
 * @property integer $id
 * @property string $action_id
 * @property string $auth_item_name
 * @property string $description
 * @property integer $lead_in_days
 * @property string $level
 * @property string $comment
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyStepToCustomField[] dutyStepToCustomFields
 * @property CustomFieldDutyStepCategory[] $customFieldDutyStepCategories
 * @property DutyData[] $dutyDatas
 * @property User $updatedBy
 * @property Action $action
 * @property AuthItem $authItemName
 * @property Level $level
 * @property DutyStepDependency[] $dutyStepDependencies
 * @property DutyStepDependency[] $dutyStepDependencies1
 * @property DutyStepDependency[] $dutyStepDependencies2
 */
class DutyStep extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Duty';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchCustomField;
	public $searchRole;
	public $searchLevel;
	/*
	 * these just here for purpose of tabs - ensuring these variables exist ensures than can be added to the url from currrent $_GET
	 */
	public $client_id;
	public $project_template_id;
	
	protected $defaultSort = array(
		't.lead_in_days'=>'DESC',
		't.description',
	);
	
	public function scopeAction($actionId)
	{
		// building something like (template_id IS NULL OR template_id = 5) AND (client_id IS NULL OR client_id = 7)
		$criteria=new DbCriteria;
		
		$criteria->compare('t.action_id', $actionId);

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
            'customFieldDutyStepCategories' => array(self::HAS_MANY, 'CustomFieldDutyStepCategory', 'duty_step_id'),
			// Beware: this missed by gii
            'dutyStepToCustomFields' => array(self::HAS_MANY, 'DutyStepToCustomField', 'duty_step_id'),
            'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'duty_step_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'action' => array(self::BELONGS_TO, 'Action', 'action_id'),
            'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'auth_item_name'),
            'level' => array(self::BELONGS_TO, 'Level', 'level'),
            'dutyStepDependencies' => array(self::HAS_MANY, 'DutyStepDependency', 'parent_duty_step_id'),
            'dutyStepDependencies1' => array(self::HAS_MANY, 'DutyStepDependency', 'action_id'),
            'dutyStepDependencies2' => array(self::HAS_MANY, 'DutyStepDependency', 'child_duty_step_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchLevel', $this->searchLevel, 'level.name', true);
		$criteria->compareAs('searchRole', $this->searchRole, 'authItemName.name', true);

		// with
		$criteria->with = array(
			'authItemName',
			'level',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
		$columns[] = 'lead_in_days';
		$columns[] = 'searchLevel';
        $columns[] = 'searchRole';
		$columns[] = 'comment';
		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'lead_in_days',
			't.description',
		);
	}
 
}

?>