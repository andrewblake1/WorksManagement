<?php

/**
 * This is the model class for table "tbl_task_to_assembly_to_task_template_to_assembly_group".
 *
 * The followings are the available columns in table 'tbl_task_to_assembly_to_task_template_to_assembly_group':
 * @property string $id
 * @property string $task_to_assembly_id
 * @property string $assembly_group_to_assembly_id
 * @property integer $assembly_group_id
 * @property integer $assembly_id
 * @property string $task_template_to_assembly_group_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskToAssembly $taskToAssembly
 * @property AssemblyGroupToAssembly $assembly
 * @property AssemblyGroupToAssembly $assemblyGroupToAssembly
 * @property TaskTemplateToAssemblyGroup $assemblyGroup
 * @property TaskTemplateToAssemblyGroup $taskTemplateToAssemblyGroup
 */
class TaskToAssemblyToTaskTemplateToAssemblyGroup extends ActiveRecord
{
	public $task_id;
	public $quantity;
//	public $task_to_assembly_id;

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
		return $this->customValidators + array(
			array('task_template_to_assembly_group_id, task_to_assembly_id, assembly_group_to_assembly_id, quantity, task_id, assembly_group_id, assembly_id, updated_by', 'required'),
			array('task_template_to_assembly_group_id, quantity, assembly_group_id, assembly_id, updated_by', 'numerical', 'integerOnly'=>true),
			array('task_to_assembly_id, task_id, task_to_assembly_id, assembly_group_to_assembly_id', 'length', 'max'=>10),
		);
	}

	public function setCustomValidators()
	{
		$this->setCustomValidatorsRange(AssemblyToAssemblyGroup::model()->findByPk($this->assembly_to_assembly_group_id));
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
            'taskToAssembly' => array(self::BELONGS_TO, 'TaskToAssembly', 'task_to_assembly_id'),
            'assembly' => array(self::BELONGS_TO, 'AssemblyGroupToAssembly', 'assembly_id'),
            'assemblyGroupToAssembly' => array(self::BELONGS_TO, 'AssemblyGroupToAssembly', 'assembly_group_to_assembly_id'),
            'assemblyGroup' => array(self::BELONGS_TO, 'TaskTemplateToAssemblyGroup', 'assembly_group_id'),
            'taskTemplateToAssemblyGroup' => array(self::BELONGS_TO, 'TaskTemplateToAssemblyGroup', 'task_template_to_assembly_group_id'),
        );
    }

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'assemblyGroup->assemblyGroup->description',
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'assembly_group_id' => 'Assembly Group',
			'assembly_group_to_assembly_id' => 'Assembly Group',
			'assembly_id' => 'Assembly',
		);
	}
	
	public function assertFromParent($modelName = null) {
		Controller::setUpdateId($this->task_to_assembly_id, 'TaskToAssembly');
		
		// need to trick it here into using task to assembly model instead as this model not in navigation hierachy
		if(!empty($this->task_to_assembly_id))
		{
			$taskToAssembly = TaskToAssembly::model()->findByPk($this->task_to_assembly_id);
			return $taskToAssembly->assertFromParent('TaskToAssembly');
		}
		
		return parent::assertFromParent($modelName);
	}
	
	public function afterFind() {
		
		// otherwise our previous saved quantity
		$task_to_assembly_id = TaskToAssembly::model()->findByPk($this->task_to_assembly_id);
		$this->quantity = $task_to_assembly_id->quantity;

		parent::afterFind();
	}
	
	public function updateSave(&$models = array()) {
		// first need to save the TaskToAssembly record as otherwise may breach a foreign key constraint - this has on update case
		$taskToAssembly = TaskToAssembly::model()->findByPk($this->task_to_assembly_id);
		$taskToAssembly->assembly_id = $this->assembly_id;
		
		if($saved = $taskToAssembly->updateSave($models))
		{
			$saved &= parent::updateSave($models);
			// need to get assembly_group_to_assembly_id which is complicated by the deleted attribute which means that more
			// than one matching row could be returned - if not for deleted attrib
			$assemblyGroupToAssembly = AssemblyGroupToAssembly::model()->findByAttributes(array('assembly_group_id'=>$this->assembly_group_id, 'assembly_id'=>$this->assembly_id));
			$this->assembly_group_to_assembly_id = $assemblyGroupToAssembly->id;
		}

		return $saved;
	}

	public function createSave(&$models=array())
	{
	
		$taskToAssembly = new TaskToAssembly;
		$taskToAssembly->attributes = $_POST['TaskToAssemblyToTaskTemplateToAssemblyGroup'];
		$taskToAssembly->parent_id = $_POST['TaskToAssemblyToTaskTemplateToAssemblyGroup']['task_to_assembly_id'];
		// filler - unused in this context but necassary in Assembly model
		$taskToAssembly->standard_id = 0;

		if($saved = $taskToAssembly->createSave($models))
		{
			$this->task_to_assembly_id = $taskToAssembly->id;
			// need to get assembly_group_to_assembly_id which is complicated by the deleted attribute which means that more
			// than one matching row could be returned - if not for deleted attrib
			$assemblyGroupToAssembly = AssemblyGroupToAssembly::model()->findByAttributes(array('assembly_group_id'=>$this->assembly_group_id, 'assembly_id'=>$this->assembly_id));
			$this->assembly_group_to_assembly_id = $assemblyGroupToAssembly->id;
			$saved &= parent::createSave($models);
		}

		return $saved;
	}
	
}