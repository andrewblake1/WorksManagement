<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property string $id
 * @property string $level
 * @property integer $project_type_id
 * @property integer $client_id
 * @property string $travel_time_1_way
 * @property string $critical_completion
 * @property string $planned
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Day[] $days
 * @property Staff $staff
 * @property ProjectType $projectType
 * @property ProjectType $client
 * @property Planning $id0
 * @property ProjectLevel $level0
 * @property ProjectToClientContact[] $projectToClientContacts
 * @property ProjectToClientContact[] $projectToClientContacts1
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 * @property ProjectToProjectTypeToAuthItem[] $projectToProjectTypeToAuthItems
 * @property Task[] $tasks
 * @property Task[] $tasks1
 */
class Project extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	public $searchInCharge;
	public $name;
	public $in_charge_id;

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
			array('project_type_id, client_id, staff_id', 'required'),
			array('project_type_id, client_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('id, level, in_charge_id', 'length', 'max'=>10),
			array('critical_completion, planned, client_id, name', 'safe'),
			array('travel_time_1_way', 'date', 'format'=>'H:m'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, level, searchInCharge, travel_time_1_way, critical_completion, planned, name, searchStaff, searchProjectType', 'safe', 'on'=>'search'),
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
			'days' => array(self::HAS_MANY, 'Day', 'project_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'client' => array(self::BELONGS_TO, 'ProjectType', 'client_id'),
			'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
			'level0' => array(self::BELONGS_TO, 'ProjectLevel', 'level'),
			'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'client_id'),
			'projectToClientContacts1' => array(self::HAS_MANY, 'ProjectToClientContact', 'project_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'project_id'),
			'projectToProjectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTypeToAuthItem', 'project_id'),
			'tasks' => array(self::HAS_MANY, 'Task', 'project_id'),
			'tasks1' => array(self::HAS_MANY, 'Task', 'client_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'in_charge_id' => 'In charge, First/Last/Email',
			'searchInCharge' => 'In charge, First/Last/Email',
			'travel_time_1_way' => 'Travel time 1 way',
			'critical_completion' => 'Critical completion',
			'planned' => 'Planned',
			'project_type_id' => 'Project type',
			'name' => 'Project name',
			'searchProjectType' => 'Project type',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			'id0.name AS name',
			"CONCAT_WS('$delimiter',
				inCharge.first_name,
				inCharge.last_name,
				inCharge.email
				) AS searchInCharge",
			'travel_time_1_way',
			't.critical_completion',
			't.planned',
			't.project_type_id',	// though not displayed, needed to get id for link field
			'projectType.description AS searchProjectType',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('t.travel_time_1_way',Yii::app()->format->toMysqlTime($this->travel_time_1_way));
		$criteria->compare('t.critical_completion',Yii::app()->format->toMysqlDate($this->critical_completion));
		$criteria->compare('t.planned',Yii::app()->format->toMysqlDate($this->planned));
		$criteria->compare('projectType.description', $this->searchProjectType, true);
		$criteria->compare('t.client_id', $this->client_id);
		$this->compositeCriteria($criteria,
			array(
				'inCharge.first_name',
				'inCharge.last_name',
				'inCharge.email',
			),
			$this->searchInCharge
		);

		// join
		$criteria->with = array(
			'projectType',
			'projectType.client',
			'id0',
			'id0.inCharge',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('id');
		$columns[] = $this->linkThisColumn('name');
        $columns[] = static::linkColumn('searchInCharge', 'Staff', 'in_charge_id');
		$columns[] = static::linkColumn('searchProjectType', 'ProjectType', 'project_type_id');
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
		return array('searchInCharge', 'searchProjectType', 'name');
	}

/*	// ensure that where possible a pk has been passed from parent
	// needed to overwrite this here because project has to look thru project type to get to client when doing update but gets client for admin
	public function assertFromParent()
	{
		// if we are in the schdule screen then they may not be a parent foreign key as will be derived when user identifies a node
		if(Yii::app()->controller->id == 'PlanningController')
		{
			return;
		}
		// if we don't have this fk attribute set
		elseif(empty($this->project_type_id) && empty($this->client_id))
		{
			$niceNameLower =  strtolower(static::getNiceName());
			throw new CHttpException(400, "No $niceNameLower identified, you must get here from the {$niceNameLower}s page");
		}
	}*/

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='id0->name';

		return $displaAttr;
	}

	public function afterFind() {
		$this->name = $this->id0->name;
//		$this->client_id = $this->projectType->client_id;
		parent::afterFind();
	}

	/**
	 * check if the current user is assigned to the given role within this project context
	 * used within business rule of checkAccess by Project task
	 */
	static function checkContext($primaryKey, $role)
	{
		// if this role exists for this project
		$ProjectToProjectTypeToAuthItem = ProjectToProjectTypeToAuthItem::model()->findAllByAttributes(array('project_id'=>$primaryKey, 'itemname'=>$role));
		if(!empty($ProjectToProjectTypeToAuthItem))
		{
			// if this user assigned this role within this project
			if($ProjectToProjectTypeToAuthItem->authAssignment->userid == Yii::app()->user->id)
			{
				 return true;
			}
		}
		
		return false;
	}
	
	public function beforeSave() {
		if(!empty($this->project_type_id))
		{
			$projectType = ProjectType::model()->findByPk($this->project_type_id);
		}
		$this->client_id = $projectType->client_id;
		
		return parent::beforeSave();
	}
}
?>