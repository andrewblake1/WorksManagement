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
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property Duty[] $duties1
 * @property TaskType $taskType
 * @property ProjectTypeToAuthItem $projectType
 * @property DutyType $dutyType
 * @property Staff $staff
 * @property ProjectTypeToAuthItem $projectTypeToAuthItem
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
	
	public function scopeTask($task_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('task.id',$task_id);
		$criteria->join='JOIN task USING(task_type_id)';
		
		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_type_to_duty_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_type_id, project_type_id, duty_type_id, project_type_to_AuthItem_id, staff_id', 'required'),
			array('task_type_id, project_type_id, duty_type_id, project_type_to_AuthItem_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_type_id, , searchDutyType, searchTaskType, searchStaff, searchProjectTypeToAuthItem', 'safe', 'on'=>'search'),
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
			'duties' => array(self::HAS_MANY, 'Duty', 'task_type_id'),
			'duties1' => array(self::HAS_MANY, 'Duty', 'task_type_to_duty_type_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectTypeToAuthItem', 'project_type_id'),
			'dutyType' => array(self::BELONGS_TO, 'DutyType', 'duty_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectTypeToAuthItem' => array(self::BELONGS_TO, 'ProjectTypeToAuthItem', 'project_type_to_AuthItem_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Task type to duty type',
			'naturalKey' => 'Client/Project type/Task type/Duty type/Role',
			'duty_type_id' => 'Duty type',
			'searchDutyType' => 'Duty type',
			'task_type_id' => 'Client/Project type/Task type',
			'searchTaskType' => 'Client/Project type/Task type',
			'searchProjectTypeToAuthItem' => 'Role',
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
			't.project_type_to_AuthItem_id',
			'dutyType.description AS searchDutyType',
			'projectTypeToAuthItem.AuthItem_name AS searchProjectTypeToAuthItem',
		);

		// where
		$criteria->compare('dutyType.description',$this->searchDutyType);
		$criteria->compare('projectTypeToAuthItem.AuthItem_name',$this->searchProjectTypeToAuthItem);
		$criteria->compare('t.task_type_id',$this->task_type_id);

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
        $columns[] = static::linkColumn('searchProjectTypeToAuthItem', 'AuthItem', 'project_type_to_AuthItem_id');
		
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
			$model = ProjectTypeToAuthItem::model()->findByPk($this->project_type_to_AuthItem_id);
			$this->project_type_id = $model->project_type_id;
		}

		return parent::beforeValidate();
	}
}

?>