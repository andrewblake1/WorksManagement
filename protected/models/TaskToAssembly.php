<?php

/**
 * This is the model class for table "task_to_assembly".
 *
 * The followings are the available columns in table 'task_to_assembly':
 * @property string $id
 * @property string $task_id
 * @property integer $assembly_id
 * @property integer $quantity
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property Assembly $assembly
 * @property Staff $staff
 */
class TaskToAssembly extends ActiveRecord
{
	public $searchTask;
	public $searchAssembly;

	public $store_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_to_assembly';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, task_id, assembly_id, quantity, staff_id', 'required'),
			array('store_id, assembly_id, quantity, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, searchTask, searchAssembly, quantity, searchStaff', 'safe', 'on'=>'search'),
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
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Task to assembly',
			'task_id' => 'Task',
			'searchTask' => 'Task',
			'assembly_id' => 'Assembly',
			'searchAssembly' => 'Assembly',
			'quantity' => 'Quantity',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.assembly_id',
			'assembly.description AS searchAssembly',
			't.quantity',
		);

		// where
		$criteria->compare('assembly.description',$this->searchAssembly,true);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.task_id',$this->task_id);
		
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
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchTask', 'searchAssembly');
	}
	
	static function getDisplayAttr()
	{
		return array('assembly->description');
	}

	public function afterFind() {
		$this->store_id = $this->assembly->store_id;
		
		return parent::afterFind();
	}
	
}

?>