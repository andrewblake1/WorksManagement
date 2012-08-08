<?php

/**
 * This is the model class for table "resource_type".
 *
 * The followings are the available columns in table 'resource_type':
 * @property integer $id
 * @property string $description
 * @property integer $resource_category_id
 * @property integer $maximum
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Resourcecategory $resourceCategory
 * @property Staff $staff
 * @property TaskToResourceType[] $taskToResourceTypes
 */
class ResourceType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchResourceCategory;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ResourceType the static model class
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
		return 'resource_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, maximum, staff_id', 'required'),
			array('resource_category_id, maximum, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, searchResourceCategory, maximum, deleted, searchStaff', 'safe', 'on'=>'search'),
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
			'resourceCategory' => array(self::BELONGS_TO, 'Resourcecategory', 'resource_category_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToResourceTypes' => array(self::HAS_MANY, 'TaskToResourceType', 'resource_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Resource Type',
			'resource_category_id' => 'Resource Category',
			'searchResourceCategory' => 'Resource Category',
			'maximum' => 'Maximum',
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
		$criteria->compare('resourceCategory.description',$this->searchResourceCategory);
		$criteria->compare('maximum',$this->maximum);
		
		$criteria->with = array('resourceCategory');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'description',
			'resourceCategory.description AS searchResourceCategory',
			'maximum',
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchResourceCategory');
	}
}