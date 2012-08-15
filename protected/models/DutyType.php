<?php

/**
 * This is the model class for table "duty_type".
 *
 * The followings are the available columns in table 'duty_type':
 * @property integer $id
 * @property string $description
 * @property integer $lead_in_days
 * @property integer $duty_category_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property TaskTypeToDutyType[] $taskTypeToDutyTypes
 * @property Dutycategory $dutyCategory
 * @property GenericType $genericType
 * @property Staff $staff
 */
class DutyType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutyCategory;
	public $searchGenericType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DutyType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'duty_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, staff_id', 'required'),
			array('lead_in_days, duty_category_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, lead_in_days, searchDutyCategory, searchGenericType, searchStaff', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'taskTypeToDutyTypes' => array(self::HAS_MANY, 'TaskTypeToDutyType', 'duty_type_id'),
			'dutyCategory' => array(self::BELONGS_TO, 'Dutycategory', 'duty_category_id'),
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Duty Type',
			'lead_in_days' => 'Lead In Days',
			'duty_category_id' => 'Duty Category',
			'searchDutyCategory' => 'Duty Category',
			'generic_type_id' => 'Generic Type',
			'searchGenericType' => 'Generic Type',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('lead_in_days',$this->lead_in_days);
		$criteria->compare('dutyCategory.description',$this->searchDutyCategory,true);
		$criteria->compare('genericType.description',$this->searchGenericType,true);
		
		$criteria->with = array('dutyCategory','genericType');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'description',
			'lead_in_days',
			'dutyCategory.description AS searchDutyCategory',
			'genericType.description AS searchGenericType',
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDutyCategory', 'searchGenericType');
	}
}