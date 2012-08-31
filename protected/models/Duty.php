<?php

/**
 * This is the model class for table "duty".
 *
 * The followings are the available columns in table 'duty':
 * @property string $id
 * @property string $task_id
 * @property integer $task_type_id
 * @property string $updated
 * @property string $generic_id
 * @property integer $staff_id
 * @property integer $task_type_to_duty_type_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property TaskTypeToDutyType $taskType
 * @property Generic $generic
 * @property Staff $staff
 * @property TaskTypeToDutyType $taskTypeToDutyType
 */
class Duty extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	public $searchTaskTypeToDutyType;
	public $searchGeneric;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'duty';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, task_type_id, staff_id, task_type_to_duty_type_id', 'required'),
			array('task_type_id, staff_id, task_type_to_duty_type_id', 'numerical', 'integerOnly'=>true),
			array('task_id, generic_id', 'length', 'max'=>10),
			array('updated', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, searchTask, searchTaskTypeToDutyType, updated, searchGeneric, searchStaff', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskTypeToDutyType', 'task_type_id'),
			'generic' => array(self::BELONGS_TO, 'Generic', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskTypeToDutyType' => array(self::BELONGS_TO, 'TaskTypeToDutyType', 'task_type_to_duty_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Duty',
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'task_type_id' => 'Task type',
			'task_type_to_duty_type_id' => 'Duty/Role/First/Last/Email',
			'searchTaskTypeToDutyType' => 'Duty/Role/First/Last/Email',
			'updated' => 'Complete',
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
			't.task_type_to_duty_type_id',
			"CONCAT_WS('$delimiter',
				dutyType.description,
				authAssignment.itemname,
				user.first_name,
				user.last_name,
				user.email
				) AS searchTaskTypeToDutyType",
			't.updated',
		);

		// where
		$this->compositeCriteria(
			$criteria,
			array(
				'dutyType.description',
				'authAssignment.itemname',
				'user.first_name',
				'user.last_name',
				'user.email',
			), $this->searchTaskTypeToDutyType);
		$criteria->compare('t.updated',Yii::app()->format->toMysqlDateTime($this->updated));
		$criteria->compare('t.task_id',$this->task_id);

		// NB: without this the has_many relations aren't returned and some select columns don't exist
		$criteria->together = true;

		// join
		$criteria->with = array(
			'task.project.projectToProjectTypeToAuthItems.authAssignment',
			'task.project.projectToProjectTypeToAuthItems.authAssignment.user',
			'taskTypeToDutyType.dutyType',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchTaskTypeToDutyType', 'TaskTypeToDutyType', 'task_type_to_duty_type_id');
		$columns[] = 'updated:datetime';
		
		return $columns;
	}

	static function getDisplayAttr()
	{
		return array('taskTypeToDutyType->dutyType->description');
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'searchTaskTypeToDutyType', 'searchGeneric');
	}

	public function beforeValidate()
	{
		if(isset($this->task_type_to_duty_type_id))
		{
			$model = TaskTypeToDutyType::model()->findByPk($this->task_type_to_duty_type_id);
			$this->task_type_id = $model->task_type_id ;
		}
		
		return parent::beforeValidate();
	}

	public function beforeSave()
	{
		// if the updated attribute was null but is now being set
		if($this->updated == 1 && $this->getOldAttributeValue('updated') == null)
		{
			// set to current datetime
			$this->updated = date('Y-m-d H:i:s');
		}
		// system admin clear
		elseif(empty($this->updated) && Yii::app()->user->checkAccess('system admin'))
		{
			// clear
			$this->updated = null;
		}
		
		return parent::beforeSave();
	}

}

?>