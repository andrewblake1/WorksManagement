<?php

/**
 * This is the model class for table "resourcecategory".
 *
 * The followings are the available columns in table 'resourcecategory':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property integer $duty_category_id
 * @property string $description
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ResourceType[] $resourceTypes
 * @property Dutycategory $dutyCategory
 * @property Staff $staff
 */
class Resourcecategory extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutyCategory;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Resourcecategory the static model class
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
		return 'resourcecategory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('lft, rgt, level, description, staff_id', 'required'),
			array('root, lft, rgt, level, duty_category_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, root, lft, rgt, level, searchDutyCategory, description, deleted, searchStaff', 'safe', 'on'=>'search'),
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
			'resourceTypes' => array(self::HAS_MANY, 'ResourceType', 'resource_category_id'),
			'dutyCategory' => array(self::BELONGS_TO, 'Dutycategory', 'duty_category_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Resource Category',
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'duty_category_id' => 'Duty Category',
			'searchDutyCategory' => 'Duty Category',
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
		$criteria->compare('root',$this->root);
		$criteria->compare('lft',$this->lft);
		$criteria->compare('rgt',$this->rgt);
		$criteria->compare('level',$this->level);
		$criteria->compare('dutyCategory.description',$this->searchDutyCategory);
		$criteria->compare('description',$this->description,true);
		
		$criteria->with = array('dutyCategory');

		$criteria->select=array(
			'id',
			'root',
			'lft',
			'rgt',
			'level',
			'dutyCategory.description AS searchDutyCategory',
			'description',
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDutyCategory');
	}
}