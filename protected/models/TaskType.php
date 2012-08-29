<?php

/**
 * This is the model class for table "task_type".
 *
 * The followings are the available columns in table 'task_type':
 * @property integer $id
 * @property string $description
 * @property integer $project_type_id
 * @property string $template_task_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericTaskType[] $genericTaskTypes
 * @property Task[] $tasks
 * @property Staff $staff
 * @property Task $templateTask
 * @property ProjectType $projectType
 * @property TaskTypeToAssembly[] $taskTypeToAssemblies
 * @property TaskTypeToDutyType[] $taskTypeToDutyTypes
 * @property TaskTypeToMaterial[] $taskTypeToMaterials
 * @property TaskTypeToResourceType[] $taskTypeToResourceTypes
 */
class TaskType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	public $searchTemplateTask;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, project_type_id, staff_id', 'required'),
			array('project_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('template_task_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, project_type_id, deleted, searchTemplateTask, searchProjectType, searchStaff, template_task_id', 'safe', 'on'=>'search'),
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
			'genericTaskTypes' => array(self::HAS_MANY, 'GenericTaskType', 'task_type_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'task_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'templateTask' => array(self::BELONGS_TO, 'Task', 'template_task_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'taskTypeToAssemblies' => array(self::HAS_MANY, 'TaskTypeToAssembly', 'task_type_id'),
			'taskTypeToDutyTypes' => array(self::HAS_MANY, 'TaskTypeToDutyType', 'task_type_id'),
			'taskTypeToMaterials' => array(self::HAS_MANY, 'TaskTypeToMaterial', 'task_type_id'),
			'taskTypeToResourceTypes' => array(self::HAS_MANY, 'TaskTypeToResourceType', 'task_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Task Type',
			'project_type_id' => 'Client/Project type',
			'searchProjectType' => 'Client/Project type',
			'template_task_id' => 'Template task',
			'searchTemplateTask' => 'Template task',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$criteria->select=array(
			't.description',
		);

		// where
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.template_task_id',$this->template_task_id,true);
		$criteria->compare('t.project_type_id',$this->project_type_id);
		
		// join
		$criteria->with = array('projectType', 'projectType.client', 'templateTask');

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'description';
 		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchClient', 'searchTemplateTask');
	}

}

?>