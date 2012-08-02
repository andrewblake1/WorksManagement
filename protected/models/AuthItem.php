<?php

/**
 * This is the model class for table "AuthItem".
 *
 * The followings are the available columns in table 'AuthItem':
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AuthAssignment[] $authAssignments
 * @property Staff $staff
 * @property AuthItemChild[] $authItemchildren
 * @property AuthItemChild[] $authItemchildren1
 * @property ClientToTaskTypeToDutyType[] $clientToTaskTypeToDutyTypes
 */
class AuthItem extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuthItem the static model class
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
		return 'AuthItem';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type', 'required'),
			array('type, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>64),
			array('description, bizrule, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, type, description, bizrule, data, searchStaff', 'safe', 'on'=>'search'),
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
			'authAssignments' => array(self::HAS_MANY, 'AuthAssignment', 'itemname'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'authItemchildren' => array(self::HAS_MANY, 'AuthItemChild', 'parent'),
			'authItemchildren1' => array(self::HAS_MANY, 'AuthItemChild', 'child'),
			'clientToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ClientToTaskTypeToDutyType', 'AuthItem_name'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'name' => 'Role',
			'type' => 'Type',
			'bizrule' => 'Bizrule',
			'data' => 'Data',
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

		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('bizrule',$this->bizrule,true);
		$criteria->compare('data',$this->data,true);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";
		
		$criteria->with = array('staff');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'name',
			'type',
			'description',
			'bizrule',
			'data',
			"CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff",
		);

		return $criteria;
	}
}