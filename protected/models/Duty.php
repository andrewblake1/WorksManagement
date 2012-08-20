<?php

/**
 * This is the model class for table "duty".
 *
 * The followings are the available columns in table 'duty':
 * @property string $id
 * @property string $task_id
 * @property string $project_to_AuthAssignment_to_task_type_to_duty_type_id
 * @property string $updated
 * @property string $generic_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property Generic $generic
 * @property Staff $staff
 * @property ProjectToAuthAssignmentToTaskTypeToDutyType $projectToAuthAssignmentToTaskTypeToDutyType
 */
class Duty extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	public $searchProjectToAuthAssignmentToTaskTypeToDutyType;
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
			array('task_id, project_to_AuthAssignment_to_task_type_to_duty_type_id, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id, project_to_AuthAssignment_to_task_type_to_duty_type_id, generic_id', 'length', 'max'=>10),
			array('updated', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchTask, searchProjectToAuthAssignmentToTaskTypeToDutyType, updated, searchGeneric, searchStaff', 'safe', 'on'=>'search'),
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
			'projectToAuthAssignmentToTaskTypeToDutyType' => array(self::BELONGS_TO, 'ProjectToAuthAssignmentToTaskTypeToDutyType', 'project_to_AuthAssignment_to_task_type_to_duty_type_id'),
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
			'project_to_AuthAssignment_to_task_type_to_duty_type_id' => 'Role/First/Last/Email/Duty type',
			'searchProjectToAuthAssignmentToTaskTypeToDutyType' => 'Role/First/Last/Email/Duty type',
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
		$criteria=new CDbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
//			't.id',
			"CONCAT_WS('$delimiter',
				authAssignment.itemname,
				user.first_name,
				user.last_name,
				user.email,
				dutyType.description
				) AS searchProjectToAuthAssignmentToTaskTypeToDutyType",
			't.updated',
			'generic.id AS searchGeneric',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$this->compositeCriteria(
			$criteria,
			array(
				'authAssignment.itemname',
				'user.first_name',
				'user.last_name',
				'user.email',
				'dutyType.description',
			), $this->searchProjectToAuthAssignmentToTaskTypeToDutyType);
		$criteria->compare('t.updated',$this->updated,true);
		$criteria->compare('t.generic.id',$this->searchGeneric);

		if(isset($this->task_id))
		{
			$criteria->compare('t.task_id',$this->task_id);
		}
		else
		{
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name
				project.description,
				task.description
				) AS searchTask";
			$this->compositeCriteria($criteria,
				array(
					'client.name',
					'project.description',
					'task.description'
				),
				$this->searchTask
			);
		}

		// join
		$criteria->with = array(
			'task',
			'projectToAuthAssignmentToTaskTypeToDutyType.projectToAuthAssignment.project',
			'projectToAuthAssignmentToTaskTypeToDutyType.projectToAuthAssignment.authAssignment',
			'projectToAuthAssignmentToTaskTypeToDutyType.projectToAuthAssignment.authAssignment.user',
			'projectToAuthAssignmentToTaskTypeToDutyType.taskTypeToDutyType.taskType.projectType.client',
//			'projectToAuthAssignmentToTaskTypeToDutyType.taskTypeToDutyType.taskType',
			'projectToAuthAssignmentToTaskTypeToDutyType.taskTypeToDutyType.dutyType',
			'generic',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		if(!isset($this->task_id))
		{
			$columns[] = array(
				'name'=>'searchTask',
				'value'=>'CHtml::link($data->searchTask,
					Yii::app()->createUrl("Task/update", array("id"=>$data->task_id))
				)',
				'type'=>'raw',
			);
		}
        $columns[] = array(
			'name'=>'searchProjectToAuthAssignmentToTaskTypeToDutyType',
			'value'=>'CHtml::link($data->searchProjectToAuthAssignmentToTaskTypeToDutyType,
				Yii::app()->createUrl("ProjectToAuthAssignmentToTaskTypeToDutyType/update", array("id"=>$data->project_to_AuthAssignment_to_task_type_to_duty_type_id))
			)',
			'type'=>'raw',
		);
		$columns[] = 'updated';
        $columns[] = array(
			'name'=>'searchGeneric',
			'value'=>'CHtml::link($data->searchGeneric,
				Yii::app()->createUrl("Generic/update", array("id"=>$data->generic_id))
			)',
			'type'=>'raw',
		);
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'searchProjectToAuthAssignmentToTaskTypeToDutyType', 'searchGeneric');
	}

}

?>