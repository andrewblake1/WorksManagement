<?php

/**
 * This is the model class for table "tbl_task_to_assembly_to_assembly_to_assembly_group".
 *
 * The followings are the available columns in table 'tbl_task_to_assembly_to_assembly_to_assembly_group':
 * @property string $id
 * @property string $task_id
 * @property string $task_to_assembly_id
 * @property integer $assembly_id
 * @property string $assembly_group_to_assembly_id
 * @property integer $assembly_group_id
 * @property string $assembly_to_assembly_group_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property TaskToAssembly $taskToAssembly
 * @property AssemblyGroupToAssembly $assembly
 * @property AssemblyToAssemblyGroup $assemblyToAssemblyGroup
 * @property AssemblyGroupToAssembly $assemblyGroup
 * @property AssemblyGroupToAssembly $assemblyGroupToAssembly
 * @property TaskToAssembly $task
 */
class TaskToAssemblyToAssemblyToAssemblyGroup extends ActiveRecord
{
	use RangeActiveRecordTrait;

	public $quantity;
	public $searchAssemblyGroup;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';
	/**
	 * @var string label for tab and breadcrumbs when creating
	 */
	static $createLabel = 'Select assembly';
	/**
	 * @var string label on button in create view
	 */
	static $createButtonText = 'Save';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(array('task_to_assembly_id', 'assembly_group_to_assembly_id')), array(
			array('quantity', 'required'),
			array('quantity', 'numerical', 'integerOnly'=>true),
		));
	}

	public function setCustomValidators()
	{
		$rangeModel = AssemblyToAssemblyGroup::model()->findByPk($this->assembly_to_assembly_group_id);
		
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskToAssembly' => array(self::BELONGS_TO, 'TaskToAssembly', 'task_to_assembly_id'),
            'assembly' => array(self::BELONGS_TO, 'AssemblyGroupToAssembly', 'assembly_id'),
            'assemblyToAssemblyGroup' => array(self::BELONGS_TO, 'AssemblyToAssemblyGroup', 'assembly_to_assembly_group_id'),
            'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroup', 'assembly_group_id'),
            'assemblyGroupToAssembly' => array(self::BELONGS_TO, 'AssemblyGroupToAssembly', 'assembly_group_to_assembly_id'),
            'task' => array(self::BELONGS_TO, 'Task', 'task_id'),
        );
    }

	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchAssemblyGroup', $this->searchAssemblyGroup, 'assemblyGroup.description', true);

		$criteria->with = array(
			'assemblyGroup',
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchAssemblyGroup',
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'assembly_group_to_assembly_id' => 'Assembly Group',
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
		$this->task_id = $task_to_assembly_id->task_id;

		parent::afterFind();
	}
	
	public function updateSave(&$models = array()) {
		// first need to save the TaskToAssembly record as otherwise may breach a foreign key constraint - this has on update case
		$taskToAssembly = TaskToAssembly::model()->findByPk($this->task_to_assembly_id);
		$taskToAssembly->attributes = $_POST[__CLASS__];
		
		if($saved = $taskToAssembly->id ? $taskToAssembly->updateSave($models) : $taskToAssembly->createSave($models))
		{
			$this->task_to_assembly_id = $taskToAssembly->id;
			// need to get assembly_group_to_assembly_id which is complicated by the deleted attribute which means that more
			// than one matching row could be returned - if not for deleted attrib
			$assemblyGroupToAssembly = AssemblyGroupToAssembly::model()->findByAttributes(array('assembly_group_id'=>$this->assembly_group_id, 'assembly_id'=>$this->assembly_id));
			$this->assembly_group_to_assembly_id = $assemblyGroupToAssembly->id;
			$saved &= parent::updateSave($models);
		}

		return $saved;
	}

/*	public function createSave(&$models=array())
	{
	
		$taskToAssembly = new TaskToAssembly;
		$taskToAssembly->attributes = $_POST['TaskToAssemblyToAssemblyToAssemblyGroup'];
		$taskToAssembly->parent_id = $_POST['TaskToAssemblyToAssemblyToAssemblyGroup']['task_to_assembly_id'];
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
	}*/
	
}