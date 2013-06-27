<?php

/**
 * This is the model class for table "tbl_task_to_material_to_task_template_to_material_group".
 *
 * The followings are the available columns in table 'tbl_task_to_material_to_task_template_to_material_group':
 * @property string $id
 * @property string $task_to_material_id
 * @property integer $material_group_to_material_id
 * @property integer $material_group_id
 * @property integer $material_id
 * @property integer $task_template_to_material_group_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskToMaterial $taskToMaterial
 * @property MaterialGroupToMaterial $material
 * @property MaterialGroupToMaterial $materialGroupToMaterial
 * @property TaskTemplateToMaterialGroup $materialGroup
 * @property TaskTemplateToMaterialGroup $taskTemplateToMaterialGroup
 */
class TaskToMaterialToTaskTemplateToMaterialGroup extends ActiveRecord
{
	public $task_id;
	public $quantity;

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
		return array_merge(parent::rules(), array(
			array('task_template_to_material_group_id, quantity, task_id, material_group_id, material_id, updated_by', 'required'),
			array('task_template_to_material_group_id, quantity, material_group_id, material_id, material_group_to_material_id, updated_by', 'numerical', 'integerOnly'=>true),
			array('task_id, task_to_material_id', 'length', 'max'=>10),
		));
	}

	public function setCustomValidators()
	{
		$this->rangeModel = TaskTemplateToMaterialGroup::model()->findByPk($this->task_template_to_material_group_id);
		
		parent::setCustomValidators();
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskToMaterial' => array(self::BELONGS_TO, 'TaskToMaterial', 'task_to_material_id'),
            'material' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_id'),
            'materialGroupToMaterial' => array(self::BELONGS_TO, 'MaterialGroupToMaterial', 'material_group_to_material_id'),
            'materialGroup' => array(self::BELONGS_TO, 'TaskTemplateToMaterialGroup', 'material_group_id'),
            'taskTemplateToMaterialGroup' => array(self::BELONGS_TO, 'TaskTemplateToMaterialGroup', 'task_template_to_material_group_id'),
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
		
		// need to trick it here into using task to material model instead as this model not in navigation hierachy
		if(!empty($this->task_to_material_id))
		{
			Controller::setUpdateId($this->task_to_material_id, 'TaskToMaterial');
			$taskToMaterial = TaskToMaterial::model()->findByPk($this->task_to_material_id);
			return $taskToMaterial->assertFromParent('TaskToMaterial');
		}
		elseif(!empty($this->task_id))
		{
			Controller::setUpdateId($this->task_id, 'Task');
			$task = Task::model()->findByPk($this->task_id);
			return $task->assertFromParent('Task');
		}
		
		return parent::assertFromParent($modelName);
	}
	
	public function afterFind() {
		
		$task_to_material_id = TaskToMaterial::model()->findByPk($this->task_to_material_id);
		$this->quantity = $task_to_material_id->quantity;

		parent::afterFind();
	}
	
	public function updateSave(&$models = array()) {
		// first need to save the TaskToAssembly record as otherwise may breach a foreign key constraint - this has on update case
		$taskToMaterial = TaskToMaterial::model()->findByPk($this->task_to_material_id);
		$taskToMaterial->attributes = $_POST[__CLASS__];
		
		if($saved = $taskToMaterial->updateSave($models))
		{
			$saved &= parent::updateSave($models);
		}

		return $saved;
	}

	public function createSave(&$models=array())
	{
		$taskToMaterial = new TaskToMaterial;
		$taskToMaterial->attributes = $_POST['TaskToMaterialToTaskTemplateToMaterialGroup'];
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