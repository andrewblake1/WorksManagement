<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property string $id
 * @property string $description
 * @property integer $project_type_id
 * @property string $travel_time_1_way
 * @property string $critical_completion
 * @property string $planned
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property ProjectType $projectType
 * @property ProjectToAuthAssignment[] $projectToAuthAssignments
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property ProjectType[] $projectTypes
 * @property Task[] $tasks
 */
class Project extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	/**
	 * @var integer $project_type_id dummy value used when creating projects
	 * but not needed needed to be persistant
	 */
	public $project_type_id;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Project the static model class
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
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, staff_id', 'required'),
			array('project_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('travel_time_1_way, critical_completion, planned', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, travel_time_1_way, critical_completion, planned, searchStaff', 'safe', 'on'=>'search'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'projectToAuthAssignments' => array(self::HAS_MANY, 'ProjectToAuthAssignment', 'project_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'project_id'),
			'projectTypes' => array(self::HAS_MANY, 'ProjectType', 'template_project_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'project_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project',
			'travel_time_1_way' => 'Travel Time 1 Way',
			'critical_completion' => 'Critical Completion',
			'planned' => 'Planned',
			'project_type_id' => 'Project Type (Client/Type)',
			'searchProjectType' => 'Project Type (Client/Type)',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('travel_time_1_way',$this->travel_time_1_way,true);
		$criteria->compare('critical_completion',$this->critical_completion,true);
		$criteria->compare('planned',$this->planned,true);

		$delimiter = Yii::app()->params['delimiter']['search'];
		$criteria->select=array(
			'id',
			'description',
			'travel_time_1_way',
			'critical_completion',
			'planned',
			"CONCAT_WS('$delimiter',
				'client.name',
				'projectType.description'
				) AS searchProjectType",
		);

		return $criteria;
	}


}