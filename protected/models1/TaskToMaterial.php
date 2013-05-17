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
	public $standard_id;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return $this->customValidators + array(
			array('standard_id, material_id, task_id', 'required'),
			array('standard_id, material_id, quantity', 'numerical', 'integerOnly'=>true),
			array('task_id, task_to_assembly_id', 'length', 'max'=>10),
		);
	}

	public function setCustomValidators()
	{
		if(!empty($this->taskToMaterialToAssemblyToMaterials))
		{
			// validate quantity against related assemblyToMaterial record
			$this->setCustomValidatorsRange($this->taskToMaterialToAssemblyToMaterials[0]->assemblyToMaterial);
		}
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'material_id' => 'Material',
			'searchTask' => 'Task',
			'searchMaterialDescription' => 'Material',
			'searchMaterialUnit' => 'Unit',
			'searchMaterialAlias' => 'Alias',
			'searchAssemblyQuantity' => 'Assembly quantity',
			'search_task_quantity' => 'Task quantity',
			'search_total_quantity' => 'Total',
			'searchMaterialGroup' => 'Group',
			'search_assembly' => 'Assembly',
			'searchStage' => 'Stage',
			'task_id' => 'Task',
			'task_to_assembly_id' => 'Assembly',
		));
	}

	static function getDisplayAttr()
	{
		return array(
			'material->description',
			'material->unit',
			'material->alias',
		);
	}

	public function afterFind() {
		$this->standard_id = $this->material->standard_id;
		
		return parent::afterFind();
	}
	
}

?>