<?php

/**
 * This is the model class for table "project_to_AuthAssignment_to_task_type_to_duty_type".
 *
 * The followings are the available columns in table 'project_to_AuthAssignment_to_task_type_to_duty_type':
 * @property string $id
 * @property string $project_to_AuthAssignment_id
 * @property integer $task_type_to_duty_type_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property ProjectToAuthAssignment $projectToAuthAssignment
 * @property TaskTypeToDutyType $taskTypeToDutyType
 * @property Staff $staff
 */
class ProjectToAuthAssignmentToTaskTypeToDutyType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectToAuthAssignment;
	public $searchTaskTypeToDutyType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectToAuthAssignmentToTaskTypeToDutyType the static model class
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
		return 'project_to_AuthAssignment_to_task_type_to_duty_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_to_AuthAssignment_id, task_type_to_duty_type_id, staff_id', 'required'),
			array('task_type_to_duty_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_to_AuthAssignment_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchProjectToAuthAssignment, searchTaskTypeToDutyType, searchStaff', 'safe', 'on'=>'search'),
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
			'duties' => array(self::HAS_MANY, 'Duty', 'project_to_AuthAssignment_to_task_type_to_duty_type_id'),
			'projectToAuthAssignment' => array(self::BELONGS_TO, 'ProjectToAuthAssignment', 'project_to_AuthAssignment_id'),
			'taskTypeToDutyType' => array(self::BELONGS_TO, 'TaskTypeToDutyType', 'task_type_to_duty_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project To Auth Assignment To Task Type To Duty Type',
			'project_to_AuthAssignment_id' => 'Project To Auth Assignment (Project/Role/First/Last/Email)',
			'searchProjectToAuthAssignment' => 'Project To Auth Assignment (Project/Role/First/Last/Email)',
			'task_type_to_duty_type_id' => 'Task Type To Duty Type (Client/Task type/Duty Type)',
			'searchTaskTypeToDutyType' => 'Task Type To Duty Type (Client/Task type/Duty Type)',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$this->compositeCriteria(
			$criteria,
			array(
				'projectToAuthAssignment.project.id',
				'projectToAuthAssignment.authAssignment.itemname',
				'projectToAuthAssignment.authAssignment.user.first_name',
				'projectToAuthAssignment.authAssignment.user.last_name',
				'projectToAuthAssignment.authAssignment.user.email'
			),
			$this->searchProjectToAuthAssignment
		);
		$this->compositeCriteria(
			$criteria,
			array(
			'taskTypeToDutyType.taskType.client.name',
			'taskTypeToDutyType.taskType.taskType.description',
			'taskTypeToDutyType.dutyType.description',
			),
			$this->searchTaskTypeToDutyType
		);

		$criteria->with = array(
			'projectToAuthAssignment.project',
			'projectToAuthAssignment.authAssignment',
			'taskTypeToDutyType.taskType.client',
			'taskTypeToDutyType.taskType.taskType',
			'taskTypeToDutyType.dutyType',
			);

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			"CONCAT_WS('$delimiter',
				projectToAuthAssignment.project.id,
				projectToAuthAssignment.authAssignment.itemname,
				projectToAuthAssignment.authAssignment.user.first_name,
				projectToAuthAssignment.authAssignment.user.last_name,
				projectToAuthAssignment.authAssignment.user.email
				) AS searchProjectToAuthAssignment",
			"CONCAT_WS('$delimiter',
				taskTypeToDutyType.taskType.client.name,
				taskTypeToDutyType.taskType.taskType.description,
				taskTypeToDutyType.dutyType.description,
				) AS searchTaskTypeToDutyType",
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'projectToAuthAssignment->project'=>'id',
			'projectToAuthAssignment->authAssignment'=>'itemname',
			'projectToAuthAssignment->authAssignment->user'=>'first_name',
			'projectToAuthAssignment->authAssignment->user'=>'last_name',
			'projectToAuthAssignment->authAssignment->user'=>'email',
			'taskTypeToDutyType->taskType->client'=>'name',
			'taskTypeToDutyType->taskType->taskType'=>'description',
			'taskTypeToDutyType->dutyType'=>'description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchProjectToAuthAssignment', 'searchTaskTypeToDutyType');
	}
}