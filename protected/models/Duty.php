<?php

/**
 * This is the model class for table "duty".
 *
 * The followings are the available columns in table 'duty':
 * @property string $id
 * @property string $task_id
 * @property string $project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id
 * @property string $updated
 * @property string $generic_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property Generic $generic
 * @property Staff $staff
 * @property ProjectToAuthAssignmentToClientToTaskTypeToDutyType $projectToAuthAssignmentToClientToTaskTypeToDutyType
 */
class Duty extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	public $searchProjectToAuthAssignmentToClientToTaskTypeToDutyType;
	public $searchGeneric;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Duty the static model class
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
			array('task_id, project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id, generic_id, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id, project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id, generic_id', 'length', 'max'=>10),
			array('updated', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchTask, searchProjectToAuthAssignmentToClientToTaskTypeToDutyType, updated, searchGeneric, searchStaff', 'safe', 'on'=>'search'),
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
			'generic' => array(self::BELONGS_TO, 'Generic', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToAuthAssignmentToClientToTaskTypeToDutyType' => array(self::BELONGS_TO, 'ProjectToAuthAssignmentToClientToTaskTypeToDutyType', 'project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id'),
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
			'project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id' => 'Project To Auth Assignment To Client To Task Type To Duty Type',
			'searchProjectToAuthAssignmentToClientToTaskTypeToDutyType' => 'Project To Auth Assignment To Client To Task Type To Duty Type',
			'updated' => 'Updated',
			'generic_id' => 'Generic',
			'searchGeneric' => 'Generic',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('task.description',$this->searchTask,true);
		$this->compositeCriteria(
			$criteria,
			array(
				'projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.project.id',
				'projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.itemname',
				'projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.user.first_name',
				'projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.user.last_name',
				'projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.user.email',
				'projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.clientToTaskType.client.name',
				'projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.clientToTaskType.taskType.description',
				'projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.dutyType.description',
			), $this->searchProjectToAuthAssignmentToClientToTaskTypeToDutyType);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('generic.id',$this->searchGeneric,true);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";

		$criteria->with = array(
			'staff',
			'projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.project',
			'projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment',
			'projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.user',
			'projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.clientToTaskType.client',
			'projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.clientToTaskType.taskType',
			'projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.dutyType',
			'generic',
		);

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'task.description',
			"CONCAT_WS('$delimiter',
				projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.project.id,
				projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.itemname,
				projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.user.first_name,
				projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.user.last_name,
				projectToAuthAssignmentToClientToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.user.email,
				projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.clientToTaskType.client.name,
				projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.clientToTaskType.taskType.description,
				projectToAuthAssignmentToClientToTaskTypeToDutyType.clientToTaskTypeToDutyType.dutyType.description
				) AS searchProjectToAuthAssignmentToClientToTaskTypeToDutyType",
			'updated',
			'generic.id AS searchGeneric',
			"CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff",
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'searchProjectToAuthAssignmentToClientToTaskTypeToDutyType', 'searchGeneric');
	}

}