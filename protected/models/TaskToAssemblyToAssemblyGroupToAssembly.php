<?php

/**
 * This is the model class for table "task_to_assembly_to_assembly_group_to_assembly".
 *
 * The followings are the available columns in table 'task_to_assembly_to_assembly_group_to_assembly':
 * @property string $id
 * @property string $task_to_assembly_id
 * @property integer $assembly_group_id
 * @property integer $assembly_id
 * @property integer $assembly_to_assembly_group_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property TaskToAssembly $taskToAssembly
 * @property AssemblyGroupToAssembly $assembly
 * @property Staff $staff
 * @property AssemblyToAssemblyGroup $assemblyToAssemblyGroup
 * @property AssemblyGroupToAssembly $assemblyGroup
 */
class TaskToAssemblyToAssemblyGroupToAssembly extends ActiveRecord
{
	public $task_id;
	public $quantity;
	public $task_to_assembly_id;

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
			array('assembly_to_assembly_group_id, task_to_assembly_id, quantity, task_id, assembly_group_id, assembly_id, staff_id', 'required'),
			array('assembly_to_assembly_group_id, quantity, assembly_group_id, assembly_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('task_to_assembly_id, task_id, task_to_assembly_id', 'length', 'max'=>10),
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
			'taskToAssembly' => array(self::BELONGS_TO, 'TaskToAssembly', 'task_to_assembly_id'),
			'assembly' => array(self::BELONGS_TO, 'AssemblyGroupToAssembly', 'assembly_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'assemblyToAssemblyGroup' => array(self::BELONGS_TO, 'AssemblyToAssemblyGroup', 'assembly_to_assembly_group_id'),
			'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroupToAssembly', 'assembly_group_id'),
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
			'task_to_assembly_id' => 'Task To Assembly',
			'assembly_group_id' => 'Assembly Group',
			'assembly_id' => 'Assembly',
		);
	}
	
	public function assertFromParent($modelName = null) {
		Controller::$nav['update']['TaskToAssembly'] = $this->task_to_assembly_id;;
		
		// need to trick it here into using task to assembly model instead as this model not in navigation hierachy
		if(!empty($this->task_to_assembly_id))
		{
			$taskToAssembly = TaskToAssembly::model()->findByPk($this->task_to_assembly_id);
			return $taskToAssembly->assertFromParent('TaskToAssembly');
		}
		
		return parent::assertFromParent($modelName);
	}
}