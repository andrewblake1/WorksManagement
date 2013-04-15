<?php

/**
 * This is the model class for table "task_to_assembly".
 *
 * The followings are the available columns in table 'task_to_assembly':
 * @property string $id
 * @property string $task_id
 * @property integer $assembly_id
 * @property string $parent_id
 * @property integer $quantity
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property Staff $staff
 * @property Assembly $assembly
 * @property TaskToAssembly $parent
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToAssemblyToAssemblyGroupToAssembly[] $taskToAssemblyToAssemblyGroupToAssemblies
 * @property TaskToMaterial[] $taskToMaterials
 */
class TaskToAssembly extends AdjacencyListActiveRecord
{
	public $searchAssembly;
	public $searchQuantity;
	public $store_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, quantity, assembly_id', 'required'),
			array('assembly_id, quantity', 'numerical', 'integerOnly'=>true),
			array('parent_id, task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, task_id, searchQuantity, searchAssembly, parent_id, assembly_id', 'safe', 'on'=>'search'),
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
			'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'task_to_assembly_id'),
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
			'searchQuantity' => 'quantity',
			'searchAssemblyGroup' => 'Group/comment',
		));
	}

/*	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'parent_id';
		// link to admin displaying children or if no children then just description without link
        $this->linkColumnAdjacencyList('searchAssembly', $columns);
		$columns[] = 'searchQuantity';
		
		return $columns;
	}*/

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
/*	public function getSearchSort()
	{
		return array(
			't.id',
//			'searchAssembly',
//			'searchQuantity',
		);
	}*/
	
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