<?php

/**
 * This is the model class for table "task_to_resource_type".
 *
 * The followings are the available columns in table 'task_to_resource_type':
 * @property string $id
 * @property string $task_id
 * @property integer $resource_type_id
 * @property integer $quantity
 * @property integer $hours
 * @property string $start
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property ResourceType $resourceType
 * @property Staff $staff
 */
class TaskToResourceType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTask;
	public $searchResourceType;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource';

	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_to_resource_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, resource_type_id, quantity, hours, staff_id', 'required'),
			array('resource_type_id, quantity, hours, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id', 'length', 'max'=>10),
			array('start', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, searchTask, searchResourceType, quantity, hours, start, searchStaff', 'safe', 'on'=>'search'),
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
			'resourceType' => array(self::BELONGS_TO, 'ResourceType', 'resource_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Task to resource type',
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'resource_type_id' => 'Resource type',
			'searchResourceType' => 'Resource type',
			'quantity' => 'Quantity',
			'hours' => 'Hours',
			'start' => 'Start',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$criteria->select=array(
			'resourceType.description AS searchResourceType',
			't.quantity',
			't.hours',
			't.start',
		);

		// where
		$criteria->compare('resourceType.description',$this->searchResourceType,true);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.hours',$this->hours);
		$criteria->compare('start',$this->start);
		$criteria->compare('t.task_id',$this->task_id);
		
		//  join
		$criteria->with = array(
			'resourceType',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = array(
			'name'=>'searchResourceType',
			'value'=>'CHtml::link($data->searchResourceType,
				Yii::app()->createUrl("ResourceType/update", array("id"=>$data->resource_type_id))
			)',
			'type'=>'raw',
		);
		$columns[] = 'quantity';
		$columns[] = 'hours';
		$columns[] = 'start';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'searchResourceType');
	}
}

?>