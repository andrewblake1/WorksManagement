<?php

/**
 * This is the model class for table "tbl_duty_step_dependency".
 *
 * The followings are the available columns in table 'tbl_duty_step_dependency':
 * @property string $id
 * @property integer $parent_duty_step_id
 * @property integer $child_duty_step_id
 * @property string $action_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property DutyStep $parentDutyStep
 * @property DutyStep $action
 * @property DutyStep $childDutyStep
 */
class DutyStepDependency extends ActiveRecord
{
	/*
	 * these just here for purpose of tabs - ensuring these variables exist ensures than can be added to the url from currrent $_GET
	 */
	public $client_id;
	public $project_template_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Dependency';

	public $searchDependsOn;
	public $searchLoopBack;
	public $searchDisplay;
	public $searchLeadInDays;
	
	protected $defaultSort = array(
		'childDutyStep.lead_in_days'=>'DESC',
		'childDutyStep.description',
	);
	
	public function rules($ignores = array())
	{
		return parent::rules(array('depth'));
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
            'parentDutyStep' => array(self::BELONGS_TO, 'DutyStep', 'parent_duty_step_id'),
            'action' => array(self::BELONGS_TO, 'DutyStep', 'action_id'),
            'childDutyStep' => array(self::BELONGS_TO, 'DutyStep', 'child_duty_step_id'),
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels($attributeLabels = array())
    {
 		return parent::attributeLabels(array(
            'child_duty_step_id' => 'Depends on',
            'parent_duty_step_id' => 'Depended on by',
        ));
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this, array('parent_duty_step_id'));

		$criteria->compareAs('searchLeadInDays', $this->searchLeadInDays, 'childDutyStep.lead_in_days');
		$criteria->compareAs('searchDisplay', $this->searchDisplay, 'childDutyStep.description', true);
		// to determine if loop back target - this depth will be greater than the lowest depth of the child_duty_step
		$criteria->compareAs('searchDependsOn', $this->searchDependsOn, 'IF((SELECT MIN(depth) FROM tbl_duty_step_dependency WHERE child_duty_step_id = t.child_duty_step_id) < t.depth, NULL, childDutyStep.description)', true);
		$criteria->compareAs('searchLoopBack', $this->searchLoopBack, 'IF((SELECT MIN(depth) FROM tbl_duty_step_dependency WHERE child_duty_step_id = t.child_duty_step_id) < t.depth, childDutyStep.description, NULL)', true);
		$criteria->compareNull('t.parent_duty_step_id',$this->parent_duty_step_id);

		$criteria->with = array(
			'childDutyStep',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchLeadInDays';
		$columns[] = 'searchDependsOn';
		$columns[] = 'searchLoopBack';
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'searchDisplay',
		);
	}
 
	/**
	 * Returns foreign key attribute name within this model that references another model.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel)
	{
		return parent::getParentForeignKey($referencesModel, array('DutyStep'=>'parent_duty_step_id'));
	}	

}