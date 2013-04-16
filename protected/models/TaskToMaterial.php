<?php

/**
 * This is the model class for table "task_to_material".
 *
 * The followings are the available columns in table 'task_to_material':
 * @property string $id
 * @property integer $quantity
 * @property string $task_id
 * @property integer $material_id
 * @property string $task_to_assembly_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Task $task
 * @property Material $material
 * @property Staff $staff
 * @property TaskToAssembly $taskToAssembly
 * @property TaskToMaterialToAssemblyToMaterial[] $taskToMaterialToAssemblyToMaterials
 * @property TaskToMaterialToMaterialGroupToMaterial[] $taskToMaterialToMaterialGroupToMaterials
 * @property TaskToMaterialToMaterialGroupToMaterial[] $taskToMaterialToMaterialGroupToMaterials1
 */
class TaskToMaterial extends ActiveRecord
{
	public $store_id;

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
			array('store_id, material_id, task_id, quantity', 'required'),
			array('store_id, material_id, quantity', 'numerical', 'integerOnly'=>true),
			array('task_id, task_to_assembly_id', 'length', 'max'=>10),
		);
	}

	public function setCustomValidators()
	{
		if(!empty($this->taskToMaterialToAssemblyToMaterials))
		{
			$assemblyToMaterial = $this->taskToMaterialToAssemblyToMaterials[0]->assemblyToMaterial;

			if(empty($assemblyToMaterial->select))
			{
				$this->customValidators[] = array('quantity', 'numerical', 'min'=>$assemblyToMaterial->minimum, 'max'=>$assemblyToMaterial->maximum);
			}
			else
			{
				$this->customValidators[] = array('quantity', 'in', 'range'=>explode(',', $assemblyToMaterial->select));
			}

			// force a re-read of validators
			$this->getValidators(NULL, TRUE);
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
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToAssembly' => array(self::BELONGS_TO, 'TaskToAssembly', 'task_to_assembly_id'),
			'taskToMaterialToAssemblyToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterial', 'task_to_material_id'),
			'taskToMaterialToMaterialGroupToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToMaterialGroupToMaterial', 'material_id'),
			'taskToMaterialToMaterialGroupToMaterials1' => array(self::HAS_MANY, 'TaskToMaterialToMaterialGroupToMaterial', 'task_to_material_id'),
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
			'searchMaterial' => 'Material',
			'searchMaterialGroup' => 'Group/comment',
			'searchAssembly' => 'Assembly',
//			'searchQuantityTooltip' => 'Comment',
			'searchStage' => 'Stage',
			'task_id' => 'Task',
			'task_to_assembly_id' => 'Assembly',
		));
	}

	static function getDisplayAttr()
	{
		return array('material->description');
	}

	public function afterFind() {
		$this->store_id = $this->material->store_id;
		
		return parent::afterFind();
	}
}

?>