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
 * @property TaskToMaterial[] $taskToMaterials
 * @property Task $task
 * @property Staff $staff
 * @property Assembly $assembly
 * @property TaskToAssembly $parent
 * @property TaskToAssembly[] $taskToAssemblies
 */
class TaskToAssembly extends AdjacencyListActiveRecord
{
	public $searchAssembly;
	public $searchComment;
	public $store_id;
	public $quantity;
	protected $defaultSort = array('t.parent_id', 'searchAssembly');

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
			array('id, task_id, searchComment, searchAssembly, parent_id, assembly_id', 'safe', 'on'=>'search'),
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
			'searchComment' => 'Comment',
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
			'subAssembly.comment AS searchComment',
		);

		// where
		$criteria->compare('t.task_id',$this->task_id,false);
		$criteria->compare('assembly.description',$this->searchAssembly,true);
		$criteria->compare('subAssembly.comment',$this->searchComment,true);
		if(!empty($this->parent_id))
		{
			$criteria->compare('t.parent_id',$this->parent_id);
		}

		// join
		// This join is to get at the comment contained within the sub assembly (assembly to assembly) table but realizing there is
		// a relationship between a parent child relationship in this table and the sub assembly table
		$criteria->join = '
			LEFT JOIN task_to_assembly taskToAssemblyParent ON t.parent_id = taskToAssemblyParent.id
			LEFT JOIN sub_assembly subAssembly
				ON taskToAssemblyParent.assembly_id = subAssembly.parent_assembly_id
				AND t.assembly_id = subAssembly.child_assembly_id
		';
		
		// join
		$criteria->with = array(
			'assembly',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'parent_id';
		// link to admin displaying children or if no children then just description without link
        $this->linkColumnAdjacencyList('searchAssembly', $columns);
		$columns[] = 'searchComment';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			't.id',
			'searchAssembly',
			'searchComment',
		);
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