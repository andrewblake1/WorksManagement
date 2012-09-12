<?php

/**
 * This is the model class for table "project_type_to_AuthItem".
 *
 * The followings are the available columns in table 'project_type_to_AuthItem':
 * @property integer $id
 * @property integer $project_type_id
 * @property string $AuthItem_name
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ProjectToProjectTypeToAuthItem[] $projectToProjectTypeToAuthItems
 * @property ProjectType $projectType
 * @property AuthItem $authItemName
 * @property Staff $staff
 * @property TaskTypeToDutyType[] $taskTypeToDutyTypes
 * @property TaskTypeToDutyType[] $taskTypeToDutyTypes1
 */
class ProjectTypeToAuthItem extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	public function scopeTaskTypeToDutyType($task_type_id)
	{
//TODO this and another location that restrict lists need to include the current record if updating rather than excluding it
// also add deleted to unique indexes and make deleted += 1 so not violating constraints when adding new and removing old multiple times
		$criteria=new CDbCriteria;
		$criteria->compare('task_type.id',$task_type_id);
//		$criteria->addCondition('taskTypeToDutyType.id IS NULL');
		$criteria->join='
			JOIN task_type
			USING ( project_type_id )
			';//LEFT JOIN task_type_to_duty_type taskTypeToDutyType ON taskTypeToDutyType.project_type_to_AuthItem_id = t.id
		$criteria->distinct=true;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project_type_to_AuthItem';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_type_id, AuthItem_name, staff_id', 'required'),
			array('project_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('AuthItem_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_type_id, AuthItem_name, deleted, staff_id', 'safe', 'on'=>'search'),
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
			'projectToProjectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTypeToAuthItem', 'project_type_to_AuthItem_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'AuthItem_name'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskTypeToDutyTypes' => array(self::HAS_MANY, 'TaskTypeToDutyType', 'project_type_id'),
			'taskTypeToDutyTypes1' => array(self::HAS_MANY, 'TaskTypeToDutyType', 'project_type_to_AuthItem_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project type to role',
			'project_type_id' => 'Project type',
			'AuthItem_name' => 'Role',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$criteria->select=array(
			'AuthItem_name',
		);

		// where
		$criteria->compare('t.AuthItem_name',$this->AuthItem_name,true);
		$criteria->compare('t.project_type_id',$this->project_type_id);

		return $criteria;
	}
	
	public function getAdminColumns()
	{
		$columns[] = 'AuthItem_name';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='AuthItem_name';

		return $displaAttr;
	}

}