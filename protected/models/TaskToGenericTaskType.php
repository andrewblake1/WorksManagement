<?php

/**
 * This is the model class for table "task_to_generic_task_type".
 *
 * The followings are the available columns in table 'task_to_generic_task_type':
 * @property string $id
 * @property integer $generic_task_type_id
 * @property string $task_id
 * @property string $generic_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Generic $generic
 * @property Staff $staff
 * @property GenericTaskType $genericTaskType
 * @property Task $task
 */
class TaskToGenericTaskType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchGenericTaskType;
	public $searchTask;
	public $searchGeneric;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom type';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_to_generic_task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('generic_task_type_id, task_id, staff_id', 'required'),
			array('generic_task_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id, generic_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, searchGenericTaskType, searchTask, searchGeneric, searchStaff', 'safe', 'on'=>'search'),
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
			'generic' => array(self::BELONGS_TO, 'Generic', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'genericTaskType' => array(self::BELONGS_TO, 'GenericTaskType', 'generic_task_type_id'),
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Task to generic task type',
			'generic_task_type_id' => 'Task type/Custom type)',
			'searchGenericTaskType' => 'Task type/Custom type)',
			'task_id' => 'Client/Task',
			'searchTask' => 'Client/Task',
			'generic_id' => 'Generic',
			'searchGeneric' => 'Generic',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.generic_task_type_id',
			"CONCAT_WS('$delimiter',
				taskType.description,
				genericType.description
				) AS searchGenericTaskType",
		);

		// where
		$this->compositeCriteria($criteria, array(
			'taskType.description',
			'genericType.description',
			), $this->searchGenericTaskType);
		$criteria->compare('t.task_id',$this->task_id);

		// join
		$criteria->with = array(
			'genericTaskType.taskType',
			'genericTaskType.genericType',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchGenericTaskType', 'GenericTaskType', 'generic_task_type_id');
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array('genericTaskType->genericType->description');
	}
	
	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchGenericTaskType', 'searchTask', 'searchGeneric');
	}
}

?>