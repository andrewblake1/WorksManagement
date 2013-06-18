<?php

/**
 * This is the model class for table "tbl_duty_step_dependency".
 *
 * The followings are the available columns in table 'tbl_duty_step_dependency':
 * @property string $id
 * @property integer $parent_duty_step_id
 * @property integer $child_duty_step_id
 * @property string $action_id
 * @property integer $deleted
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

	public $searchChildDutyStep;
	public $searchLeadInDays;
	protected $defaultSort = array(
		'childDutyStep.leas_in_days'=>'DESC',
		'childDutyStep.description',
	);
	
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
		return array_merge(parent::rules(), array(
            array('child_duty_step_id, action_id, updated_by', 'required'),
            array('parent_duty_step_id, child_duty_step_id, deleted, updated_by', 'numerical', 'integerOnly'=>true),
            array('id, action_id', 'length', 'max'=>10),
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
            'parentDutyStep' => array(self::BELONGS_TO, 'DutyStep', 'parent_duty_step_id'),
            'action' => array(self::BELONGS_TO, 'DutyStep', 'action_id'),
            'childDutyStep' => array(self::BELONGS_TO, 'DutyStep', 'child_duty_step_id'),
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'child_duty_step_id' => 'Depends on',
            'action_id' => 'Action',
			'searchChildDutyStep' => 'Depends on',
			'searchLeadInDays' => 'Lead in days',
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			't.action_id',
			't.parent_duty_step_id',
			't.child_duty_step_id',
			'childDutyStep.description AS searchChildDutyStep',
			'childDutyStep.lead_in_days AS searchLeadInDays',
		);

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.action_id',$this->action_id);
		$criteria->compareNull('t.parent_duty_step_id',$this->parent_duty_step_id);
		$criteria->compare('childDutyStep.description',$this->searchChildDutyStep,true);
		$criteria->compare('childDutyStep.lead_in_days',$this->searchLeadInDays);

		$criteria->with = array(
			'childDutyStep',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchLeadInDays';
		$columns[] = static::linkColumn('searchChildDutyStep', 'DutyStep', 'child_duty_step_id');
 		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'childDutyStep->description',
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