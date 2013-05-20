<?php

/**
 * This is the model class for table "tbl_duty_type".
 *
 * The followings are the available columns in table 'tbl_duty_type':
 * @property integer $id
 * @property integer $parent_id
 * @property string $description
 * @property integer $lead_in_days
 * @property string $level
 * @property integer $duty_category_id
 * @property integer $custom_field_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property DutyData[] $dutyDatas
 * @property DutyData[] $dutyDatas1
 * @property DutyCategory $dutyCategory
 * @property CustomField $customField
 * @property User $updatedBy
 * @property DutyType $parent
 * @property DutyType[] $dutyTypes
 * @property TaskTemplateToDutyType[] $taskTemplateToDutyTypes
 */
class DutyType extends AdjacencyListActiveRecord
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
			array('parent_id, lead_in_days, duty_category_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('level', 'length', 'max'=>10),
			array('custom_field_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, parent_id, searchIntegralTo, description, lead_in_days, level, duty_category_id, searchDutyCategory, searchCustomField', 'safe', 'on'=>'search'),
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
            'dutyDatas1' => array(self::HAS_MANY, 'DutyData', 'duty_type_id'),
            'dutyCategory' => array(self::BELONGS_TO, 'DutyCategory', 'duty_category_id'),
            'customField' => array(self::BELONGS_TO, 'CustomField', 'custom_field_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'parent' => array(self::BELONGS_TO, 'DutyType', 'parent_id'),
            'dutyTypes' => array(self::HAS_MANY, 'DutyType', 'parent_id'),
            'taskTemplateToDutyTypes' => array(self::HAS_MANY, 'TaskTemplateToDutyType', 'duty_type_id'),
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
			'parent_id' => 'Integral to', 
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
			't.lead_in_days',
			't.level',
			'customField.description AS searchCustomField',
			'parent.description AS searchIntegralTo',
			't.parent_id',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.lead_in_days',$this->lead_in_days);
		$criteria->compare('customField.description',$this->searchCustomField,true);
		$criteria->compare('t.duty_category_id', $this->duty_category_id);
		$criteria->compare('t.level',$this->level,true);
		$criteria->compare('parent.description',$this->searchIntegralTo,true);
		
		// with
		$criteria->with = array(
			'customField',
			'parent',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('description');
		$columns[] = static::linkColumn('searchIntegralTo', 'DutyType', 'parent_id');
		$columns[] = 'lead_in_days';
		$columns[] = 'level';
        $columns[] = static::linkColumn('searchCustomField', 'CustomField', 'custom_field_id');
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchCustomField',
			'searchIntegralTo',
		);
	}
}

?>