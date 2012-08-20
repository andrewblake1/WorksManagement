<?php

/**
 * This is the model class for table "reschedule".
 *
 * The followings are the available columns in table 'reschedule':
 * @property string $id
 * @property string $task_id
 * @property string $new_task_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property Task $newTask
 * @property Staff $staff
 */
class Reschedule extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
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
			array('task_id, new_task_id, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id, new_task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchTask, $searchNewTask, $searchStaff', 'safe', 'on'=>'search'),
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
			'task_id' => 'Old task',
			'searchTask' => 'Old task',
			'new_task_id' => 'New task',
			'searchNewTask' => 'New task',
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
				client.name,
				project.description,
				newTask.description
				) AS searchNewTask"
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$this->compositeCriteria($criteria,
			array(
				'client.name',
				'project.description',
				'newTask.description',
			),
			$this->searchNewTask
		);

		if(isset($this->task_id))
		{
			$criteria->compare('t.task_id',$this->task_id);
		}
		else
		{
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
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
		$criteria->with = array('task','newTask', 'task.project', 'task.project.projectType.client');

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
			'name'=>'searchNewTask',
			'value'=>'CHtml::link($data->searchNewTask,
				Yii::app()->createUrl("Task/update", array("id"=>$data->new_task_id))
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
		return array('searchTask', 'searchNewTask');
	}
}

?>