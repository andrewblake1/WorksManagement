<?php

/**
 * This is the model class for table "reschedule".
 *
 * The followings are the available columns in table 'reschedule':
 * @property string $id
 * @property string $old_task_id
 * @property string $new_task_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $oldTask
 * @property Task $newTask
 * @property Staff $staff
 */
class Reschedule extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchOldTask;
	public $searchNewTask;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Reschedule the static model class
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
		return 'reschedule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('old_task_id, new_task_id, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('old_task_id, new_task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchOldTask, $searchNewTask, $searchStaff', 'safe', 'on'=>'search'),
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
			'oldTask' => array(self::BELONGS_TO, 'Task', 'old_task_id'),
			'newTask' => array(self::BELONGS_TO, 'Task', 'new_task_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Reschedule',
			'old_task_id' => 'Old Task',
			'searchOldTask' => 'Old Task',
			'new_task_id' => 'New Task',
			'searchNewTask' => 'New Task',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('oldTask.description',$this->searchOldTask,true);
		$criteria->compare('newTask.description',$this->searchNewTask,true);
		$criteria->with = array('oldTask','newTask');

		$delimiter = Yii::app()->params['delimiter']['search'];

		$criteria->select=array(
			'id',
			'oldTask.description AS searchOldTask',
			'newTask.description AS searchNewTask',
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchOldTask', 'searchNewTask');
	}
}