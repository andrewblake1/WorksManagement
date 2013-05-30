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
 * @property integer $custom_field_id
 * @property string $comment
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyData[] $dutyDatas
 * @property CustomField $customField
 * @property User $updatedBy
 * @property Action $action
 * @property AuthItem $authItemName
 * @property Level $level0
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
	public $searchAuthItem;
	public $searchLevel;
	/*
	 * these just here for purpose of tabs - ensuring these variables exist ensures than can be added to the url from currrent $_GET
	 */
	public $client_id;
	public $project_template_id;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
            array('action_id, description', 'required'),
            array('lead_in_days', 'numerical', 'integerOnly'=>true),
            array('action_id, level', 'length', 'max'=>10),
            array('description', 'length', 'max'=>64),
            array('comment', 'length', 'max'=>255),
            array('custom_field_id, project_template_id, client_id, auth_item_name', 'safe'),
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
            'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'level'),
            'customField' => array(self::BELONGS_TO, 'CustomField', 'custom_field_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'action' => array(self::BELONGS_TO, 'Action', 'action_id'),
            'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'auth_item_name'),
            'level0' => array(self::BELONGS_TO, 'Level', 'level'),
            'dutyStepDependencies' => array(self::HAS_MANY, 'DutyStepDependency', 'action_id'),
            'dutyStepDependencies1' => array(self::HAS_MANY, 'DutyStepDependency', 'parent_duty_step_id'),
            'dutyStepDependencies2' => array(self::HAS_MANY, 'DutyStepDependency', 'child_duty_step_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'lead_in_days' => 'Lead in days',
			'searchIntegralTo' => 'Integral to', 
			'custom_field_id' => 'Custom field',
			'searchCustomField' => 'Custom field',
			'searchAuthItem' => 'Role',
			'searchLevel' => 'Level',
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
			't.custom_field_id',
			't.description',
			't.comment',
			't.lead_in_days',
			'level0.description AS searchLevel',
			'customField.description AS searchCustomField',
			'authItemName.name AS searchAuthItem',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.comment',$this->comment,true);
		$criteria->compare('t.lead_in_days',$this->lead_in_days);
		$criteria->compare('customField.description',$this->searchCustomField,true);
		$criteria->compare('level0.description',$this->searchLevel,true);
		$criteria->compare('authItemName.name',$this->searchAuthItem, true);
		
		// with
		$criteria->with = array(
			'customField',
			'authItemName',
			'level0',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'lead_in_days';
		$columns[] = 'searchLevel';
        $columns[] = 'searchAuthItem';
        $columns[] = static::linkColumn('searchCustomField', 'CustomField', 'custom_field_id');
		$columns[] = 'comment';
		
		return $columns;
	}
	
/*
	public function beforeValidate()
	{
		// need to set project_template_id which is an extra foreign key to make circular foreign key constraint
		if(isset($this->project_template_to_auth_item_id))
		{
			$projectTemplateToAuthItem = ProjectTemplateToAuthItem::model()->findByPk($this->project_template_to_auth_item_id);
			$this->project_template_id = $projectTemplateToAuthItem->project_template_id;
		}

		return parent::beforeValidate();
	}*/

}

?>