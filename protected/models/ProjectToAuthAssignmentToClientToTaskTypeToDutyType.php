<?php

/**
 * This is the model class for table "project_to_AuthAssignment_to_client_to_task_type_to_duty_type".
 *
 * The followings are the available columns in table 'project_to_AuthAssignment_to_client_to_task_type_to_duty_type':
 * @property string $id
 * @property string $project_to_AuthAssignment_id
 * @property integer $client_to_task_type_to_duty_type_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property ProjectToAuthAssignment $projectToAuthAssignment
 * @property ClientToTaskTypeToDutyType $clientToTaskTypeToDutyType
 * @property Staff $staff
 */
class ProjectToAuthAssignmentToClientToTaskTypeToDutyType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectToAuthAssignment;
	public $searchClientToTaskTypeToDutyType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectToAuthAssignmentToClientToTaskTypeToDutyType the static model class
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
		return 'project_to_AuthAssignment_to_client_to_task_type_to_duty_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_to_AuthAssignment_id, client_to_task_type_to_duty_type_id, staff_id', 'required'),
			array('client_to_task_type_to_duty_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_to_AuthAssignment_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchProjectToAuthAssignment, searchClientToTaskTypeToDutyType, searchStaff', 'safe', 'on'=>'search'),
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
			'duties' => array(self::HAS_MANY, 'Duty', 'project_to_AuthAssignment_to_client_to_task_type_to_duty_type_id'),
			'projectToAuthAssignment' => array(self::BELONGS_TO, 'ProjectToAuthAssignment', 'project_to_AuthAssignment_id'),
			'clientToTaskTypeToDutyType' => array(self::BELONGS_TO, 'ClientToTaskTypeToDutyType', 'client_to_task_type_to_duty_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project To Auth Assignment To Client To Task Type To Duty Type',
			'project_to_AuthAssignment_id' => 'Project To Auth Assignment (Project/Role/First/Last/Email)',
			'searchProjectToAuthAssignment' => 'Project To Auth Assignment (Project/Role/First/Last/Email)',
			'client_to_task_type_to_duty_type_id' => 'Client To Task Type To Duty Type (Client/Task type/Duty Type)',
			'searchClientToTaskTypeToDutyType' => 'Client To Task Type To Duty Type (Client/Task type/Duty Type)',
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
			'clientToTaskTypeToDutyType.clientToTaskType.client.name',
			'clientToTaskTypeToDutyType.clientToTaskType.taskType.description',
			'clientToTaskTypeToDutyType.dutyType.description',
			),
			$this->searchClientToTaskTypeToDutyType
		);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";

		$criteria->with = array(
			'staff',
			'projectToAuthAssignment.project',
			'projectToAuthAssignment.authAssignment',
			'clientToTaskTypeToDutyType.clientToTaskType.client',
			'clientToTaskTypeToDutyType.clientToTaskType.taskType',
			'clientToTaskTypeToDutyType.dutyType',
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
				clientToTaskTypeToDutyType.clientToTaskType.client.name,
				clientToTaskTypeToDutyType.clientToTaskType.taskType.description,
				clientToTaskTypeToDutyType.dutyType.description,
				) AS searchClientToTaskTypeToDutyType",
			"CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff",
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
				'projectToAuthAssignment.project'=>array('id'),
				'projectToAuthAssignment.authAssignment'=>array('itemname'),
				'projectToAuthAssignment.authAssignment.user'=>array('first_name'),
				'projectToAuthAssignment.authAssignment.user'=>array('last_name'),
				'projectToAuthAssignment.authAssignment.user'=>array('email'),
				'clientToTaskTypeToDutyType.clientToTaskType.client'=>array('name'),
				'clientToTaskTypeToDutyType.clientToTaskType.taskType'=>array('description'),
				'clientToTaskTypeToDutyType.dutyType'=>array('description'),
		);
	}


	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchProjectToAuthAssignment', 'searchClientToTaskTypeToDutyType');
	}
}