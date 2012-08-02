<?php

/**
 * This is the model class for table "client_to_task_type_to_duty_type".
 *
 * The followings are the available columns in table 'client_to_task_type_to_duty_type':
 * @property integer $id
 * @property integer $duty_type_id
 * @property integer $client_to_task_type_id
 * @property string $AuthItem_name
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property DutyType $dutyType
 * @property AuthItem $authItemName
 * @property Staff $staff
 * @property ClientToTaskType $clientToTaskType
 * @property ProjectToAuthAssignmentToClientToTaskTypeToDutyType[] $projectToAuthAssignmentToClientToTaskTypeToDutyTypes
 */
class ClientToTaskTypeToDutyType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutyType;
	public $searchClientToTaskType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ClientToTaskTypeToDutyType the static model class
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
		return 'client_to_task_type_to_duty_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('duty_type_id, client_to_task_type_id, AuthItem_name, staff_id', 'required'),
			array('duty_type_id, client_to_task_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('AuthItem_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchDutyType, searchClientToTaskType, AuthItem_name, searchStaff', 'safe', 'on'=>'search'),
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
			'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'AuthItem_name'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'clientToTaskType' => array(self::BELONGS_TO, 'ClientToTaskType', 'client_to_task_type_id'),
			'projectToAuthAssignmentToClientToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ProjectToAuthAssignmentToClientToTaskTypeToDutyType', 'client_to_task_type_to_duty_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Client To Task Type To Duty Type',
			'naturalKey' => '(Client/Task Type/Duty Type/Role)',
			'duty_type_id' => 'Duty Type',
			'searchDutyType' => 'Duty Type',
			'client_to_task_type_id' => 'Client To Task Type (Client/Task Type)',
			'searchClientToTaskType' => 'Client To Task Type (Client/Task Type)',
			'AuthItem_name' => 'Role',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('dutyType.description',$this->searchDutyType,true);
		$this->compositeCriteria($criteria,
			array(
				'clientToTaskType.client.name',
				'clientToTaskType.taskType.description'
			),
			$this->searchClientToTaskType
		);
		$criteria->compare('AuthItem_name',$this->AuthItem_name,true);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		$criteria->scopes=array('notDeleted');

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";

		$criteria->with = array('staff','dutyType','clientToTaskType.client','clientToTaskType.taskType');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'dutyType.description AS searchDutyType',
			"CONCAT_WS('$delimiter',
				clientToTaskType.client.name,
				clientToTaskType.taskType.description
				) AS searchClientToTaskType",
			'AuthItem_name',
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
				'clientToTaskType.client'=>array('name'),
				'clientToTaskType.taskType'=>array('description'),
				'dutyType'=>array('description'),
				'AuthItem_name'
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDutyType', 'searchClientToTaskType');
	}

}