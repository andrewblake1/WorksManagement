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
 * @property ProjectType $projectType
 * @property Staff $staff
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property ProjectToProjectTypeToAuthItem[] $projectToProjectTypeToAuthItems
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
	 * @var integer $client_id may be passed via get for search
	 */
	public $client_id;
	
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
			array('description, project_type_id, staff_id', 'required'),
			array('project_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('travel_time_1_way, critical_completion, planned, client_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, travel_time_1_way, critical_completion, planned, searchStaff, searchProjectType', 'safe', 'on'=>'search'),
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
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'project_id'),
			'projectToProjectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTypeToAuthItem', 'project_id'),
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
			'travel_time_1_way' => 'Travel time 1 way',
			'critical_completion' => 'Critical completion',
			'planned' => 'Planned',
			'project_type_id' => 'Client/Project type',
			'searchProjectType' => 'Client/Project type',
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
			't.id',
			't.description',
			'travel_time_1_way',
			't.critical_completion',
			't.planned',
			'projectType.description AS searchProjectType',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.travel_time_1_way',$this->travel_time_1_way);
		$criteria->compare('t.critical_completion',$this->critical_completion);
		$criteria->compare('t.planned',$this->planned);
		$criteria->compare('projectType.description', $this->searchProjectType, true);
		$criteria->compare('client.id', $this->client_id);

		// join
		$criteria->with = array(
			'projectType',
			'projectType.client',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'id';
		$columns[] = 'description';
		$columns[] = array(
			'name'=>'searchProjectType',
			'value'=>'CHtml::link($data->searchProjectType,
				Yii::app()->createUrl("ProjectType/update", array("id"=>$data->project_type_id))
			)',
			'type'=>'raw',
		);
		$columns[] = 'travel_time_1_way';
		$columns[] = 'critical_completion';
		$columns[] = 'planned';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchProjectType');
	}

	// ensure that where possible a pk has been passed from parent
	// needed to overwrite this here because project has to look thru project type to get to client when doing update but gets client for admin
	public function assertFromParent()
	{

		// if we don't have this fk attribute set
		if(empty($this->project_type_id) && empty($this->client_id))
		{
			$niceNameLower =  strtolower(static::getNiceName());
			throw new CHttpException(400, "No $niceNameLower identified, you must get here from the {$niceNameLower}s page");
		}
		// otherwise return the fk
		else
		{
			return $parentForeignKey;
		}
	}

}

?>