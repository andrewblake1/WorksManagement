<?php

/**
 * This is the model class for table "client_to_task_type".
 *
 * The followings are the available columns in table 'client_to_task_type':
 * @property integer $id
 * @property integer $client_id
 * @property integer $task_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Client $client
 * @property TaskType $taskType
 * @property Staff $staff
 * @property ClientToTaskTypeToDutyType[] $clientToTaskTypeToDutyTypes
 * @property GenericTaskType[] $genericTaskTypes
 * @property Task[] $tasks
 */
class ClientToTaskType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchClient;
	public $searchTaskType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ClientToTaskType the static model class
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
		return 'client_to_task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, task_type_id, staff_id', 'required'),
			array('client_id, task_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchClient, searchTaskType, searchStaff', 'safe', 'on'=>'search'),
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
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'clientToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ClientToTaskTypeToDutyType', 'client_to_task_type_id'),
			'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'client_to_task_type_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'client_to_task_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Client To Task Type',
			'naturalKey' => '(Client/Task Type)',
			'client_id' => 'Client',
			'searchClient' => 'Client',
			'task_type_id' => 'Task Type',
			'searchTaskType' => 'Task Type',
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
		$criteria->compare('client.name',$this->searchClient, true);
		$criteria->compare('taskType.description',$this->searchTaskType, true);
		$this->compositeCriteria($criteria, array('staff.first_name','staff.last_name','staff.email'), $this->searchStaff);

		$criteria->scopes=array('notDeleted');

		if(!isset($_GET[__CLASS__.'_sort']))
			$criteria->order = 't.'.$this->tableSchema->primaryKey." DESC";

		$criteria->with = array('staff', 'client','taskType');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'client.name AS searchClient',
			'taskType.description AS searchTaskType',
			"CONCAT_WS('$delimiter',staff.first_name,staff.last_name,staff.email) AS searchStaff",
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array('client_id' => array('client.name'), 'task_type_id' => array('taskType.description'));
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchClient', 'searchTaskType');
	}

}
