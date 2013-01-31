<?php

/**
 * This is the model class for table "task_to_assembly".
 *
 * The followings are the available columns in table 'task_to_assembly':
 * @property string $id
 * @property string $task_id
 * @property integer $assembly_id
 * @property string $parent_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property MaterialToTask[] $materialToTasks
 * @property Task $task
 * @property Staff $staff
 * @property Assembly $assembly
 * @property TaskToAssembly $parent
 * @property TaskToAssembly[] $taskToAssemblies
 */
class TaskToAssembly extends ActiveRecord
{
	public $searchAssembly;

	public $store_id;
	public $quantity;
	
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
			array('task_id, quantity, assembly_id, staff_id', 'required'),
			array('parent_id, staff_id, assembly_id, quantity', 'numerical', 'integerOnly'=>true),
			array('task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, task_id, searchAssembly, parent_id, assembly_id, searchStaff', 'safe', 'on'=>'search'),
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
			'materialToTasks' => array(self::HAS_MANY, 'MaterialToTask', 'task_to_assembly_id'),
			'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
			'parent' => array(self::BELONGS_TO, 'TaskToAssembly', 'parent_id'),
			'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'parent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'task_id' => 'Task',
			'assembly_id' => 'Assembly',
			'searchAssembly' => 'Assembly',
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
			't.parent_id',
			'assembly.description AS searchAssembly',
		);

		// where
		$criteria->compare('assembly.description',$this->searchAssembly,true);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.task_id',$this->task_id);
		if(!empty($this->parent_id))
		{
			$criteria->compare('t.parent_id',$this->parent_id);
		}

		return $criteria;
	}

	public function getAdminColumns()
	{
		// link to admin displaying children or if no children then just description without link
        $this->linkColumnAdjacencyList('searchAssembly', $columns);
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchAssembly');
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