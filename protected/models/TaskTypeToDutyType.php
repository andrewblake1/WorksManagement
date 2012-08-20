<?php

/**
 * This is the model class for table "task_type_to_duty_type".
 *
 * The followings are the available columns in table 'task_type_to_duty_type':
 * @property integer $id
 * @property integer $duty_type_id
 * @property integer $task_type_id
 * @property string $AuthItem_name
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property DutyType $dutyType
 * @property AuthItem $authItemName
 * @property Staff $staff
 * @property TaskType $taskType
 * @property ProjectToAuthAssignmentToTaskTypeToDutyType[] $projectToAuthAssignmentToTaskTypeToDutyTypes
 */
class TaskTypeToDutyType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutyType;
	public $searchTaskType;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TaskTypeToDutyType the static model class
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
			array('duty_type_id, task_type_id, AuthItem_name, staff_id', 'required'),
			array('duty_type_id, task_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('AuthItem_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchDutyType, searchTaskType, AuthItem_name, searchStaff', 'safe', 'on'=>'search'),
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
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'projectToAuthAssignmentToTaskTypeToDutyTypes' => array(self::HAS_MANY, 'ProjectToAuthAssignmentToTaskTypeToDutyType', 'task_type_to_duty_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Task type to duty type',
			'naturalKey' => 'Client/Task type/Duty type/Role',
			'duty_type_id' => 'Duty type',
			'searchDutyType' => 'Duty type',
			'task_type_id' => 'Client/Task type',
			'searchTaskType' => 'Client/Task type',
			'AuthItem_name' => 'Role',
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
			'dutyType.description AS searchDutyType',
			't.AuthItem_name',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('dutyType.description',$this->searchDutyType,true);
		$criteria->compare('t.AuthItem_name',$this->AuthItem_name,true);

		if(isset($this->task_type_id))
		{
			$criteria->compare('t.task_type_id',$this->task_type_id);
		}
		else
		{
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
				taskType.description
				) AS searchTaskType";
			$this->compositeCriteria($criteria, array(
				'client.name',
				'taskType.description'
			), $this->searchTaskType);
		}

		// join
		$criteria->with = array('dutyType','taskType.client','taskType');

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
        $columns[] = array(
			'name'=>'searchDutyType',
			'value'=>'CHtml::link($data->searchDutyType,
				Yii::app()->createUrl("DutyType/update", array("id"=>$data->duty_type_id))
			)',
			'type'=>'raw',
		);
 		if(!isset($this->task_id_id))
		{
			$columns[] = array(
				'name'=>'searchTaskType',
				'value'=>'CHtml::link($data->searchTaskType,
					Yii::app()->createUrl("TaskType/update", array("id"=>$data->task_type_id))
				)',
				'type'=>'raw',
			);
		}
        $columns[] = array(
			'name'=>'AuthItem_name',
			'value'=>'CHtml::link($data->AuthItem_name,
				Yii::app()->createUrl("AuthItem/update", array("id"=>$data->AuthItem_name))
			)',
			'type'=>'raw',
		);
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'taskType->client'=>'name',
			'taskType'=>'description',
			'dutyType'=>'description',
			'AuthItem_name'
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDutyType', 'searchTaskType');
	}

}

?>