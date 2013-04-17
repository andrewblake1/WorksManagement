<?php

/**
 * This is the model class for table "task_type_to_duty_type".
 *
 * The followings are the available columns in table 'task_type_to_duty_type':
 * @property integer $id
 * @property integer $task_type_id
 * @property integer $project_type_id
 * @property integer $duty_type_id
 * @property integer $project_type_to_AuthItem_id
 * @property string $importance
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property DutyType $dutyType
 * @property ProjectTypeToAuthItem $projectTypeToAuthItem
 * @property TaskType $projectType
 * @property Staff $staff
 * @property TaskType $taskType
 */
class TaskTypeToDutyType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutyType;
	public $searchTaskType;
	public $searchProjectTypeToAuthItem;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Duty type';

	/**
	 * Importance. These are the emum values set by the DataType custom type within 
	 * the database
	 */
	const importanceStandard = 'Standard';
	const importanceOptional = 'Optional';
	
	/**
	 * Returns importance labels.
	 * @return array data importance - to match enum type in mysql workbench
	 */
	public static function getImportanceLabels()
	{
		return array(
			self::importanceStandard=>self::importanceStandard,
			self::importanceOptional=>self::importanceOptional,
		);
	}

	/**
	 * Returns data type column names.
	 * @return array data storage types - to match enum type in mysql workbench
	 */
	public static function getImportanceColumnNames()
	{
		return array(
			self::importanceStandard=>'importance_standard',
			self::importanceOptional=>'importance_optional',
		);
	}

	public function scopeTask($task_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('task.id',$task_id);
		$criteria->join='JOIN task USING(task_type_id)';
		
		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_type_id, project_type_id, duty_type_id, project_type_to_AuthItem_id, importance', 'required'),
			array('task_type_id, project_type_id, duty_type_id, project_type_to_AuthItem_id', 'numerical', 'integerOnly'=>true),
			array('importance', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_type_id, importance, searchDutyType, searchTaskType, searchProjectTypeToAuthItem', 'safe', 'on'=>'search'),
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
			'dutyType' => array(self::BELONGS_TO, 'DutyType', 'duty_type_id'),
			'projectTypeToAuthItem' => array(self::BELONGS_TO, 'ProjectTypeToAuthItem', 'project_type_to_AuthItem_id'),
			'projectType' => array(self::BELONGS_TO, 'TaskType', 'project_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'duty_type_id' => 'Duty type',
			'searchDutyType' => 'Duty type',
			'task_type_id' => 'Client/Project type/Task type',
			'searchTaskType' => 'Client/Project type/Task type',
			'searchProjectTypeToAuthItem' => 'Role',
			'importance' => 'Standard Optional',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.duty_type_id',
			't.project_type_to_AuthItem_id',
			'dutyType.description AS searchDutyType',
			'projectTypeToAuthItem.AuthItem_name AS searchProjectTypeToAuthItem',
			't.importance',
		);

		// where
		$criteria->compare('dutyType.description',$this->searchDutyType);
		$criteria->compare('projectTypeToAuthItem.AuthItem_name',$this->searchProjectTypeToAuthItem);
		$criteria->compare('t.task_type_id',$this->task_type_id);
		$criteria->compare('t.importance',$this->importance,true);

		// join
		$criteria->with = array(
			'dutyType',
			'taskType',
			'taskType.projectType',
			'projectTypeToAuthItem'
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchDutyType', 'DutyType', 'duty_type_id');
//        $columns[] = static::linkColumn('searchProjectTypeToAuthItem', 'ProjectTypeToAuthItem', 'project_type_to_AuthItem_id');
        $columns[] = 'searchProjectTypeToAuthItem';
		$columns[] = 'importance';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'dutyType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDutyType', 'searchProjectTypeToAuthItem');
	}

	public function beforeValidate()
	{
		// need to set project_type_id which is an extra foreign key to make circular foreign key constraint
		if(isset($this->project_type_to_AuthItem_id))
		{
			$projectTypeToAuthItem = ProjectTypeToAuthItem::model()->findByPk($this->project_type_to_AuthItem_id);
			$this->project_type_id = $projectTypeToAuthItem->project_type_id;
		}

		return parent::beforeValidate();
	}
}

?>