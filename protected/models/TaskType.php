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
 * @property ProjectType $projectType
 * @property Staff $staff
 * @property Task $templateTask
 * @property TaskTypeToDutyType[] $taskTypeToDutyTypes
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
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TaskType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

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
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'templateTask' => array(self::BELONGS_TO, 'Task', 'template_task_id'),
			'taskTypeToDutyTypes' => array(self::HAS_MANY, 'TaskTypeToDutyType', 'task_type_id'),
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
//			't.id',
			't.description',
			't.template_task_id',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.template_task_id',$this->template_task_id,true);
//		$criteria->compare('templateTask.description',$this->searchTemplateTask,true);

		if(isset($this->project_type_id))
		{
			$criteria->compare('t.project_type_id',$this->project_type_id);
		}
		else
		{
			$criteria->select[]="CONCAT_WS('$delimiter',
				client.name,
				projectType.description
				) AS searchProjectType";
			$this->compositeCriteria($criteria,
				array(
					'client.name',
					'projectType.description',
				),
				$this->searchProjectType
			);
		}
		
		// join
		$criteria->with = array('projectType', 'projectType.client', 'templateTask');

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = 'description';
  		if(!isset($this->project_type_id))
		{
			$columns[] = array(
				'name'=>'searchProjectType',
				'value'=>'CHtml::link($data->searchProjectType,
					Yii::app()->createUrl("ProjectType/update", array("id"=>$data->project_type_id))
				)',
				'type'=>'raw',
			);
		}
        $columns[] = array(
			'name'=>'searchTemplateTask',
			'value'=>'CHtml::link($data->searchTemplateTask,
				Yii::app()->createUrl("TemplateTask/update", array("id"=>$data->template_task_id))
			)',
			'type'=>'raw',
		);
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
/*		$controller = ucfirst(Yii::app()->controller->id);
		
		// show when not coming from parent
		if(!isset($_GET[$controller]['project_type_id'])  && !isset($_GET[$controller]['project_id']) && !isset($_GET[$_GET['model']]))*/
		// if this pk attribute has been passed in a higher crumb in the breadcrumb trail
		if(Yii::app()->getController()->primaryKeyInBreadCrumbTrail('task_type_id'))
		{
			ActiveRecord::$labelOverrides['task_type_id'] = 'Client/Project type/Task type';
			$displaAttr['projectType->client']='name';
		}
		else
		{
			ActiveRecord::$labelOverrides['task_type_id'] = 'Task type';
		}

		$displaAttr[]='description';

		return $displaAttr;
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