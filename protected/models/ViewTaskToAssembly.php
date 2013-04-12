<?php

class ViewTaskToAssembly extends ViewActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssemblyGroup;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'v_task_to_assembly';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, task_id, searchAssemblyGroup, description', 'safe', 'on'=>'search'),
		);
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
			't.task_id',
			't.parent_id',
			't.description',
			't.quantity',
			't.assembly_group_id',
			'assemblyGroup.description as searchAssemblyGroup',
			't.searchTaskToAssemblyToAssemblyGroupToAssemblyId',
			't.assembly_to_assembly_group_id',
		);
				
		// join
		$criteria->join = '
			LEFT JOIN assembly_group assemblyGroup ON t.assembly_group_id = assemblyGroup.id
		';
		
		// where
		$criteria->compare('description',$this->description,true);
		$criteria->compare('assemblyGroup.description',$this->searchAssemblyGroup,true);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.task_id',$this->task_id);

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = $this->linkThisColumn('description');
		$columns[] = 'searchAssemblyGroup';
		$columns[] = 'parent_id';
		$columns[] = 'quantity';
        $this->linkColumnAdjacencyList('searchAssembly', $columns, 'searchAssemblyId');

		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'description',
			'searchAssemblyGroup',
			'parent_id',
			'quantity',
		);
	}

	static function getDisplayAttr()
	{
		return TaskToAssembly::getDisplayAttr();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return TaskToAssembly::model()->attributeLabels();
	}
}

?>