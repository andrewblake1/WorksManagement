<?php

/**
 * This is the model class for table "tbl_task_to_assembly".
 *
 * The followings are the available columns in table 'tbl_task_to_assembly':
 * @property string $id
 * @property string $task_id
 * @property integer $assembly_id
 * @property integer $sub_assembly_id
 * @property string $parent_id
 * @property integer $quantity
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property User $updatedBy
 * @property Assembly $assembly
 * @property TaskToAssembly $parent
 * @property TaskToAssembly[] $taskToAssemblies
 * @property SubAssembly $subAssembly
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups
 * @property TaskToMaterial[] $taskToMaterials
 */
class TaskToAssembly extends ActiveRecord
{
	use AdjacencyListActiveRecordTrait;
	use RangeActiveRecordTrait;
	
	public $standard_id;

	public $searchDrawingId;
	
	public $searchAssembly;
	public $searchParent;
	public $searchDrawing;
	public $searchGroup;
	public $searchAliases;
	public $searchTaskQuantity;
	public $searchAccumlatedTotal;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(), array(
			array('standard_id', 'numerical', 'integerOnly'=>true),
		));
	}

	public function tableName() {

		// need to create the temp table that we will use - required to get the accumlated total - only want to do one shot though hence the atatic
		static $called = false;

		if(!$called && $this->scenario == 'search')
		{
			// generate the temp table used by the view
			Yii::app()->db->createCommand("CALL pro_planning_to_assembly({$_GET['task_id']})")->execute();
			$called = true;
		}

		return ($this->scenario == 'search') || static::$inSearch
			? 'tmp_planning_to_assembly'
			: 'tbl_task_to_assembly';
	}
	
	// needed due to database view
	public function primaryKey()
	{
		return 'id';
	}

	public function setCustomValidators()
	{
		$rangeModel = NULL;
	
		if(!empty($this->subAssembly))
		{
			// validate quantity against related assemblyToAssembly record
			$rangeModel = $this->subAssembly;
		}
		elseif(!empty($this->taskToAssemblyToAssemblyToAssemblyGroups))
		{
			// validate quantity against related assemblyToAssembly record
			$rangeModel = $this->taskToAssemblyToAssemblyToAssemblyGroups[0]->assemblyToAssemblyGroup;
		}
		elseif(!empty($this->taskToAssemblyToTaskTemplateToAssemblyGroups))
		{
			// validate quantity against related assemblyToAssembly record
			$rangeModel = $this->taskToAssemblyToTaskTemplateToAssemblyGroups[0]->taskTemplateToAssemblyGroup;
		}
		
		$this->setCustomValidatorsFromSource($rangeModel);
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'parent' => array(self::BELONGS_TO, 'TaskToAssembly', 'parent_id'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'parent_id'),
            'subAssembly' => array(self::BELONGS_TO, 'SubAssembly', 'sub_assembly_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'task_to_assembly_id'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'task_to_assembly_id'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'task_to_assembly_id'),
        );
    }

	static function getDisplayAttr()
	{
		return array(
			'searchAssembly',
			'searchAliases',
		);
	}

	public function afterFind() {
		if($this->id)
		{
			$this->standard_id = $this->assembly->standard_id;
		}
		
		return parent::afterFind();
	}
	
	/*
	 * to be overidden if using mulitple models
	 */
	public function createSave(&$models=array(), $runValidation = true)
	{
		return TaskToAssemblyController::addAssembly($this->task_id, $this->assembly_id, $this->quantity, $this->parent_id, $this->sub_assembly_id, $models, $this);
	}
	
	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);
		
		$criteria->select[] = 'assembly.drawing_id AS searchDrawingId';
		
		$criteria->compareAs('searchAssembly', $this->searchAssembly, 'assembly.description', true);
		$criteria->composite('searchAliases', $this->searchAliases, array(
			'clientToAssembly.alias',
			'assembly.alias'
		));

		// update
		if(($this->tableName()) == 'tbl_task_to_assembly')
		{
			$criteria->join = '
				JOIN tbl_assembly assembly ON t.assembly_id = assembly.id
				JOIN tbl_task task ON t.task_id = task.id
				JOIN tbl_project project ON task.project_id = project.id
				LEFT JOIN tbl_client_to_assembly clientToAssembly ON project.client_id = clientToAssembly.client_id
					AND t.assembly_id = clientToAssembly.assembly_id
			';

			return $criteria;
		}
		
		// admin
		$criteria->compareAs('searchDrawing', $this->searchDrawing, 'drawing.description', true);
		$criteria->compareAs('searchParent', $this->searchParent, 'assemblyParent.description', true);
		$criteria->compareAs('searchTaskQuantity', $this->searchTaskQuantity, 'task.quantity', true);
		$criteria->compareAs('searchAccumlatedTotal', $this->searchAccumlatedTotal, 'task.quantity * t.quantity', true);
		$criteria->composite('searchGroup', $this->searchGroup, array(
			'assemblyGroup.description',
			't.comment'
		));

		// join
		$criteria->join = '
			LEFT JOIN tbl_assembly_group assemblyGroup ON t.assembly_group_id = assemblyGroup.id
			LEFT JOIN tbl_assembly assembly ON t.assembly_id = assembly.id
			LEFT JOIN tbl_drawing drawing ON assembly.drawing_id = drawing.id
			LEFT JOIN tbl_task task ON t.task_id = task.id
			LEFT JOIN tbl_project project ON task.project_id = project.id
			LEFT JOIN tbl_client_to_assembly clientToAssembly ON project.client_id = clientToAssembly.client_id
				AND t.assembly_id = clientToAssembly.assembly_id
			LEFT JOIN tbl_task_to_assembly parent ON t.parent_id = parent.id
			LEFT JOIN tbl_assembly assemblyParent ON parent.assembly_id = assemblyParent.id
		';

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = static::linkColumn('searchAssembly', 'TaskToAssembly', 'id');
		$columns[] = static::linkColumn('searchParent', 'TaskToAssembly', 'parent_id');
		$columns[] = static::linkColumn('searchDrawing', 'Drawing', 'searchDrawingId');
 		$columns[] = 'searchAliases';
 		$columns[] = 'item';
		$columns[] = static::linkColumn('searchGroup', 'AssemblyGroup', 'assembly_group_id');
		$columns[] = 'quantity';
		$columns[] = 'searchTaskQuantity';
		$columns[] = 'searchAccumlatedTotal';

		return $columns;
	}

/*	public function delete()
	{
		// can't use cascade delete on fk's for these as also need the ability if reseting selection to null to just delete this record and not cascade
		// need to bear in mind bulk deletes hence this works best here like this
		$command = Yii::app()->db->createCommand('DELETE FROM tbl_task_to_assembly_to_assembly_to_assembly_group WHERE task_to_assembly_id = :task_to_assembly_id');
		$temp = $this->id;
		$command->bindParam(':task_to_assembly_id', $temp);
		$command->execute();
		$command = Yii::app()->db->createCommand('DELETE FROM tbl_task_to_assembly_to_task_template_to_assembly_group WHERE task_to_assembly_id = :task_to_assembly_id');
		$temp = $this->id;
		$command->bindParam(':task_to_assembly_id', $temp);
		$command->execute();
		
		return parent::delete();
	}*/

}

?>