<?php

/**
 * This is the model class for table "tbl_task_to_material".
 *
 * The followings are the available columns in table 'tbl_task_to_material':
 * @property string $id
 * @property integer $quantity
 * @property string $task_id
 * @property integer $material_id
 * @property string $task_to_assembly_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property TaskToAssembly $taskToAssembly
 * @property User $updatedBy
 * @property Material $material
 * @property TaskToMaterialToAssemblyToMaterial[] $taskToMaterialToAssemblyToMaterials
 * @property TaskToMaterialToAssemblyToMaterialGroup[] $taskToMaterialToAssemblyToMaterialGroups
 * @property TaskToMaterialToTaskTemplateToMaterialGroup[] $taskToMaterialToTaskTemplateToMaterialGroups
 */
class TaskToMaterial extends ActiveRecord
{
	use RangeActiveRecordTrait;

	public $standard_id;

	public $searchStage;
	public $searchMaterial;
	public $searchUnit;
	public $searchGroup;
	public $searchAlias;
	public $searchAssemblyQuantity;
	public $searchAccumlatedTotal;
	public $searchDrawingId;
	
	

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
			? 'v_task_to_material'
			: 'tbl_task_to_material';
	}
	
	// needed due to database view
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(), array(
			array('standard_id', 'required'),
			array('standard_id', 'numerical', 'integerOnly'=>true),
		));
	}

	public function setCustomValidators()
	{
		$rangeModel = null;
		
		if(!empty($this->taskToMaterialToAssemblyToMaterials))
		{
			// validate quantity against related assemblyToMaterial record
			$rangeModel = $this->taskToMaterialToAssemblyToMaterials[0]->assemblyToMaterial;
		}
		elseif(!empty($this->taskToMaterialToAssemblyToMaterialGroups))
		{
			// validate quantity against related assemblyToMaterial record
			$rangeModel = $this->taskToMaterialToAssemblyToMaterialGroups[0]->assemblyToMaterialGroup;
		}
		elseif(!empty($this->taskToMaterialToTaskTemplateToMaterialGroups))
		{
			// validate quantity against related assemblyToMaterial record
			$rangeModel = $this->taskToMaterialToTaskTemplateToMaterialGroups[0]->taskTemplateToMaterialGroup;
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
            'taskToAssembly' => array(self::BELONGS_TO, 'TaskToAssembly', 'task_to_assembly_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
            'taskToMaterialToAssemblyToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterial', 'task_to_material_id'),
            'taskToMaterialToAssemblyToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'task_to_material_id'),
            'taskToMaterialToTaskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'task_to_material_id'),
        );
    }

	static function getDisplayAttr()
	{
		return array(
			'searchMaterial',
			'searchUnit',
			'searchAlias',
		);
	}

	public function afterFind() {
		if($this->id)
		{
			$this->standard_id = $this->material->standard_id;
		}
		
		return parent::afterFind();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'search_assembly' => 'Assembly drawing',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		// update
		if(($this->tableName()) == 'tbl_task_to_material')
		{
			$criteria->compareAs('searchMaterial', $this->searchMaterial, 'material.description', true);
			$criteria->compareAs('searchUnit', $this->searchUnit, 'material.unit', true);
			$criteria->composite('searchAlias', $this->searchAlias, array(
				'clientToMaterial.alias',
				'material.alias'
			));
			
			$criteria->join = '
				JOIN tbl_material material ON t.material_id = material.id
				JOIN tbl_task task ON t.task_id = task.id
				JOIN tbl_project project ON task.project_id = project.id
				LEFT JOIN tbl_client_to_material clientToMaterial ON project.client_id = clientToMaterial.client_id
					AND t.material_id = clientToMaterial.material_id
			';

			return $criteria;
		}
		
		// admin
		
		$criteria->select[] = 'assembly.drawing_id AS searchDrawingId';
		$criteria->select[] = 'search_task_quantity';

		$criteria->compareAs('searchStage', $this->searchStage, 'stage.description', true);
		$criteria->compareAs('searchMaterial', $this->searchMaterial, 'material.description', true);
		$criteria->compareAs('searchUnit', $this->searchUnit, 'material.unit', true);
		$criteria->compareAs('searchAccumlatedTotal', $this->searchAccumlatedTotal, 't.quantity * task.quantity * COALESCE(taskToAssembly.accumulated_total, 1)', true);
		$criteria->compareAs('searchAssemblyQuantity', $this->searchAssemblyQuantity, 'taskToAssembly.accumulated_total', true);
		$criteria->composite('searchAlias', $this->searchAlias, array(
			'clientToMaterial.alias',
			'material.alias'
		));
		$criteria->composite('searchGroup', $this->searchGroup, array(
			'materialGroup.description',
			't.comment'
		));
		
		$criteria->join = '
			LEFT JOIN tbl_stage stage ON t.stage_id = stage.id
			LEFT JOIN tbl_material_group materialGroup ON t.material_group_id = materialGroup.id
			LEFT JOIN tbl_material material ON t.material_id = material.id
			LEFT JOIN tmp_planning_to_assembly taskToAssembly ON t.task_to_assembly_id = taskToAssembly.id
			LEFT JOIN tbl_task task ON t.task_id = task.id
			LEFT JOIN tbl_project project ON task.project_id = project.id
			LEFT JOIN tbl_client_to_material clientToMaterial ON project.client_id = clientToMaterial.client_id
				AND t.material_id = clientToMaterial.material_id
			LEFT JOIN tbl_assembly assembly ON taskToAssembly.assembly_id = assembly.id
		';

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'searchMaterial';
 		$columns[] = 'searchUnit';
 		$columns[] = 'searchAlias';
		$columns[] = 'searchGroup';
		$columns[] = 'searchStage';
		$columns[] = 'quantity';
		$columns[] = 'search_task_quantity';
		$columns[] = 'searchAssemblyQuantity';
		$columns[] = 'searchAccumlatedTotal';
		$columns[] = static::linkColumn('search_assembly', 'Drawing', 'searchDrawingId');
 		$columns[] = 'item';
		
		return $columns;
	}
	
	public function delete()
	{
		// can't use cascade delete on fk's for these as also need the ability if reseting selection to null to just delete this record and not cascade
		// need to bear in mind bulk deletes hence this works best here like this
		$command = Yii::app()->db->createCommand('DELETE FROM tbl_task_to_material_to_assembly_to_material_group WHERE task_to_material_id = :task_to_material_id');
		$command->bindParam(':task_to_material_id', $temp = $this->id);
		$command->execute();
		$command = Yii::app()->db->createCommand('DELETE FROM tbl_task_to_material_to_task_template_to_material_group WHERE task_to_material_id = :task_to_material_id');
		$command->bindParam(':task_to_material_id', $temp = $this->id);
		$command->execute();
		
		return parent::delete();
	}

}

?>