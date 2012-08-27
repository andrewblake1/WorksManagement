<?php

/**
 * This is the model class for table "generic_task_type".
 *
 * The followings are the available columns in table 'generic_task_type':
 * @property integer $id
 * @property integer $task_type_id
 * @property integer $generictaskcategory_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericType $genericType
 * @property Generictaskcategory $generictaskcategory
 * @property Staff $staff
 * @property TaskType $taskType
 * @property TaskToGenericTaskType[] $taskToGenericTaskTypes
 */
class GenericTaskType extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom type';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchTaskType;
	public $searchGenerictaskcategory;
	public $searchGenericType;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'generic_task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_type_id, generic_type_id, staff_id', 'required'),
			array('task_type_id, generictaskcategory_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_type_id, searchTaskType, searchGenerictaskcategory, searchGenericType, searchStaff', 'safe', 'on'=>'search'),
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
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'generictaskcategory' => array(self::BELONGS_TO, 'Generictaskcategory', 'generictaskcategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'taskToGenericTaskTypes' => array(self::HAS_MANY, 'TaskToGenericTaskType', 'generic_task_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Generic task type',
			'task_type_id' => 'Client/Project type/Task type',
			'searchTaskType' => 'Client/Project type/Task type',
			'generictaskcategory_id' => 'Task category',
			'searchGenerictaskcategory' => 'Task category',
			'generic_type_id' => 'Custom type',
			'searchGenericType' => 'Custom type',
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
			'generictaskcategory.description AS searchGenerictaskcategory',
			'genericType.description AS searchGenericType',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('generictaskcategory.description',$this->searchGenerictaskcategory,true);
		$criteria->compare('genericType.description',$this->searchGenericType,true);

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
		$criteria->with = array(
			'taskType.projectType.client',
			'taskType',
			'generictaskcategory',
			'genericType',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
 		if(!isset($this->task_type_id))
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
			'name'=>'searchGenerictaskcategory',
			'value'=>'CHtml::link($data->searchGenerictaskcategory,
				Yii::app()->createUrl("Generictaskcategory/update", array("id"=>$data->generictaskcategory_id))
			)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchGenericType',
			'value'=>'CHtml::link($data->searchGenericType,
				Yii::app()->createUrl("GenericType/update", array("id"=>$data->generic_type_id))
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
//			'taskType->projectType->client->name',
//			'taskType->description',
			'genericType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTaskType', 'searchGenerictaskcategory', 'searchGenericType');
	}

}

?>