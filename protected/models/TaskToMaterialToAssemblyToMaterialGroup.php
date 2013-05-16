<?php

/**
 * This is the model class for table "tbl_task_to_material_to_assembly_to_material_group".
 *
 * The followings are the available columns in table 'tbl_task_to_material_to_assembly_to_material_group':
 * @property string $id
 * @property string $task_to_material_id
 * @property integer $material_group_to_material_id
 * @property integer $material_group_id
 * @property integer $material_id
 * @property integer $assembly_to_material_group_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property TaskToMaterial $taskToMaterial
 * @property MaterialGroupToMaterial $material
 * @property User $updatedBy
 * @property AssemblyToMaterialGroup $assemblyToMaterialGroup
 * @property MaterialGroupToMaterial $materialGroup
 * @property MaterialGroupToMaterial $materialGroupToMaterial
 */
class TaskToMaterialToAssemblyToMaterialGroup extends ActiveRecord
{
	public $task_id;
	public $quantity;
	public $task_to_assembly_id;

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
			array('assembly_to_material_group_id, task_to_assembly_id, quantity, task_id, material_group_id, material_id, updated_by', 'required'),
			array('assembly_to_material_group_id, quantity, material_group_id, material_id, material_group_to_material_id, updated_by', 'numerical', 'integerOnly'=>true),
			array('task_to_assembly_id, task_id, task_to_material_id', 'length', 'max'=>10),
		);
	}

	public function setCustomValidators()
	{
		$this->setCustomValidatorsRange(AssemblyToMaterialGroup::model()->findByPk($this->assembly_to_material_group_id));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'taskToMaterial' => array(self::BELONGS_TO, 'TaskToMaterial', 'task_to_material_id'),
            'material' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'assemblyToMaterialGroup' => array(self::BELONGS_TO, 'AssemblyToMaterialGroup', 'assembly_to_material_group_id'),
            'materialGroup' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_group_id'),
            'materialGroupToMaterial' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_group_to_material_id'),
        );
    }

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'materialGroup->materialGroup->description',
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'task_to_material_id' => 'Task To Material',
			'material_group_id' => 'Material Group',
			'material_group_to_material_id' => 'Material Group',
			'material_id' => 'Material',
		);
	}
	
	public function assertFromParent($modelName = null) {
		Controller::setUpdateId($this->task_to_material_id, 'TaskToMaterial');
		
		// need to trick it here into using task to material model instead as this model not in navigation hierachy
		if(!empty($this->task_to_material_id))
		{
			$taskToMaterial = TaskToMaterial::model()->findByPk($this->task_to_material_id);
			return $taskToMaterial->assertFromParent('TaskToMaterial');
		}
		
		return parent::assertFromParent('TaskToMaterial');
	}
	
	public function afterFind() {
		
		$task_to_material_id = TaskToMaterial::model()->findByPk($this->task_to_material_id);
		$this->quantity = $task_to_material_id->quantity;

		parent::afterFind();
	}
	
	public function updateSave(&$models = array()) {
		// first need to save the TaskToAssembly record as otherwise may breach a foreign key constraint - this has on update case
		$taskToMaterial = TaskToMaterial::model()->findByPk($this->task_to_material_id);
		$taskToMaterial->material_id = $this->material_id;
		
		if($saved = $taskToMaterial->updateSave($models))
		{
			$saved &= parent::updateSave($models);
		}

		return $saved;
	}

	public function createSave(&$models=array())
	{
		$taskToMaterial = new TaskToMaterial;
		$taskToMaterial->attributes = $_POST['TaskToMaterialToAssemblyToMaterialGroup'];
		// filler - unused in this context but necassary in Material model
		$taskToMaterial->standard_id = 0;

		if($saved = $taskToMaterial->createSave($models))
		{
			$this->task_to_material_id = $taskToMaterial->id;
			// need to get material_group_to_material_id which is complicated by the deleted attribute which means that more
			// than one matching row could be returned - if not for deleted attrib
			$materialGroupToMaterial = MaterialGroupToMaterial::model()->findByAttributes(array('material_group_id'=>$this->material_group_id, 'material_id'=>$this->material_id));
			$this->material_group_to_material_id = $materialGroupToMaterial->id;
			$saved &= parent::createSave($models);
		}

		return $saved;
	}

}