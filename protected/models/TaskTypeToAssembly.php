<?php

/**
 * This is the model class for table "task_type_to_assembly".
 *
 * The followings are the available columns in table 'task_type_to_assembly':
 * @property integer $id
 * @property integer $task_type_id
 * @property integer $assembly_id
 * @property integer $quantity
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property TaskType $taskType
 * @property Assembly $assembly
 * @property Staff $staff
 */
class TaskTypeToAssembly extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssembly;
	public $searchTaskType;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';

	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_type_to_assembly';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_type_id, assembly_id, quantity, staff_id', 'required'),
			array('task_type_id, assembly_id, quantity, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_type_id, searchTaskType, searchAssembly, quantity, staff_id', 'safe', 'on'=>'search'),
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
			'taskType' => array(self::BELONGS_TO, 'TaskType', 'task_type_id'),
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'task_type_id' => 'Task Type',
			'assembly_id' => 'Assembly',
			'quantity' => 'Quantity',
			'staff_id' => 'Staff',
		);
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
			't.assembly_id',
			'assembly.description AS searchAssembly',
			't.quantity',
		);

		// where
		$criteria->compare('assembly.description',$this->searchAssembly);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.task_type_id',$this->task_type_id);

		// join
		$criteria->with = array(
			'assembly',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchAssembly', 'Assembly', 'assembly_id');
		$columns[] = 'quantity';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'assembly->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchAssembly', 'searchTaskType');
	}
}