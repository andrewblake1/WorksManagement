<?php

/**
 * This is the model class for table "tbl_duty_step".
 *
 * The followings are the available columns in table 'tbl_duty_step':
 * @property integer $id
 * @property string $description
 * @property integer $lead_in_days
 * @property string $level
 * @property integer $duty_category_id
 * @property integer $custom_field_id
 * @property string $comment
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyData[] $dutyDatas
 * @property DutyCategory $dutyCategory
 * @property CustomField $customField
 * @property User $updatedBy
 * @property DutyStepDependency[] $dutyStepDependencies
 * @property DutyStepDependency[] $dutyStepDependencies1
 */
class DutyStep extends AdjacencyListActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Duty';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutyCategory;
	public $searchCustomField;
	public $searchIntegralTo;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('description', 'required'),
			array('lead_in_days, duty_category_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
            array('comment', 'length', 'max'=>255),
			array('level', 'length', 'max'=>10),
			array('custom_field_id', 'safe'),
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
            'dutyCategory' => array(self::BELONGS_TO, 'DutyCategory', 'duty_category_id'),
            'customField' => array(self::BELONGS_TO, 'CustomField', 'custom_field_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'dutyStepDependencies' => array(self::HAS_MANY, 'DutyStepDependency', 'parent_duty_step_id'),
            'dutyStepDependencies1' => array(self::HAS_MANY, 'DutyStepDependency', 'child_duty_step_id'),
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
			'duty_category_id' => 'Duty category',
			'searchDutyCategory' => 'Duty category',
			'custom_field_id' => 'Custom field',
			'searchCustomField' => 'Custom field',
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
			't.level',
			'customField.description AS searchCustomField',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.comment',$this->comment,true);
		$criteria->compare('t.lead_in_days',$this->lead_in_days);
		$criteria->compare('customField.description',$this->searchCustomField,true);
		$criteria->compare('t.duty_category_id', $this->duty_category_id);
		$criteria->compare('t.level',$this->level,true);
		
		// with
		$criteria->with = array(
			'customField',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'lead_in_days';
		$columns[] = 'level';
        $columns[] = static::linkColumn('searchCustomField', 'CustomField', 'custom_field_id');
		$columns[] = 'comment';
		
		return $columns;
	}

}

?>