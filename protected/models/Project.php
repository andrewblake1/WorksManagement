<?php

/**
 * This is the model class for table "tbl_project".
 *
 * The followings are the available columns in table 'tbl_project':
 * @property string $id
 * @property string $level
 * @property integer $project_template_id
 * @property integer $client_id
 * @property string $travel_time_1_way
 * @property string $critical_completion
 * @property string $planned
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Day[] $days
 * @property User $updatedBy
 * @property ProjectTemplate $projectTemplate
 * @property ProjectTemplate $client
 * @property Planning $level0
 * @property Planning $id0
 * @property ProjectToClientContact[] $projectToClientContacts
 * @property ProjectToClientContact[] $projectToClientContacts1
 * @property ProjectToCustomFieldToProjectTemplate[] $projectToCustomFieldToProjectTemplates
 * @property ProjectToProjectTemplateToAuthItem[] $projectToProjectTemplateToAuthItems
 * @property Task[] $tasks
 */
class Project extends CustomFieldExtensionActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectTemplate;
	public $searchInCharge;
	public $name;
	public $in_charge_id;

	protected $classModelToCustomFieldModelType = 'ProjectToCustomFieldToProjectTemplate';
	protected $attributeCustomFieldModelType_id = 'custom_field_to_project_template_id';
	protected $attributeModel_id = 'project_id';
	protected $relationCustomFieldModelType = 'customFieldToProjectTemplate';
	protected $relationCustomFieldModelTypes = 'customFieldToProjectTemplates';
	protected $relationModelType = 'projectTemplate';
	protected $relationModelToCustomFieldModelTypes = 'projectToCustomFieldToProjectTemplates';
	protected $relationModelToCustomFieldModelType = 'projectToCustomFieldToProjectTemplate';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('project_template_id, client_id', 'required'),
			array('project_template_id, client_id', 'numerical', 'integerOnly'=>true),
			array('id, level, in_charge_id', 'length', 'max'=>10),
			array('critical_completion, planned, client_id, name', 'safe'),
			array('travel_time_1_way', 'date', 'format'=>'H:m'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, level, searchInCharge, travel_time_1_way, critical_completion, planned, name, searchProjectTemplate', 'safe', 'on'=>'search'),
		));
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'projectTemplate' => array(self::BELONGS_TO, 'ProjectTemplate', 'project_template_id'),
            'client' => array(self::BELONGS_TO, 'ProjectTemplate', 'client_id'),
            'level0' => array(self::BELONGS_TO, 'Planning', 'level'),
            'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
            'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'client_id'),
            'projectToClientContacts1' => array(self::HAS_MANY, 'ProjectToClientContact', 'project_id'),
            'projectToCustomFieldToProjectTemplates' => array(self::HAS_MANY, 'ProjectToCustomFieldToProjectTemplate', 'project_id'),
            'projectToProjectTemplateToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTemplateToAuthItem', 'project_id'),
            'tasks' => array(self::HAS_MANY, 'Task', 'project_id'),
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
			'travel_time_1_way' => 'Travel time 1 way (HH:mm)',
			'critical_completion' => 'Critical completion',
			'planned' => 'Planned',
			'project_template_id' => 'Project type',
			'name' => 'Project name',
			'searchProjectTemplate' => 'Project type',
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
				contact.first_name,
				contact.last_name,
				contact.email
				) AS searchInCharge",
			'travel_time_1_way',
			'id0.in_charge_id AS in_charge_id',
			't.critical_completion',
			't.planned',
			't.project_template_id',	// though not displayed, needed to get id for link field
			'projectTemplate.description AS searchProjectTemplate',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('t.travel_time_1_way',Yii::app()->format->toMysqlTime($this->travel_time_1_way));
		$criteria->compare('t.critical_completion',Yii::app()->format->toMysqlDate($this->critical_completion));
		$criteria->compare('t.planned',Yii::app()->format->toMysqlDate($this->planned));
		$criteria->compare('projectTemplate.description', $this->searchProjectTemplate, true);
		$criteria->compare('t.client_id', $this->client_id);
		$this->compositeCriteria($criteria,
			array(
				'contact.first_name',
				'contact.last_name',
				'contact.email',
			),
			$this->searchInCharge
		);

		// with
		$criteria->with = array(
			'projectTemplate',
			'projectTemplate.client',
			'id0',
			'id0.inCharge.contact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('id');
		$columns[] = $this->linkThisColumn('name');
        $columns[] = static::linkColumn('searchInCharge', 'User', 'in_charge_id');
		$columns[] = static::linkColumn('searchProjectTemplate', 'ProjectTemplate', 'project_template_id');
		$columns[] = 'travel_time_1_way';
		$columns[] = 'critical_completion';
		$columns[] = 'planned';
		
		return $columns;
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
		elseif(empty($this->project_template_id) && empty($this->client_id))
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
//		$this->client_id = $this->projectTemplate->client_id;
		parent::afterFind();
	}

	/**
	 * check if the current user is assigned to the given role within this project context
	 * used within business rule of checkAccess by Project task
	 */
	static function checkContext($primaryKey, $role)
	{
		// if this role exists for this project
		$ProjectToProjectTemplateToAuthItem = ProjectToProjectTemplateToAuthItem::model()->findAllByAttributes(array('project_id'=>$primaryKey, 'item_name'=>$role));
		if(!empty($ProjectToProjectTemplateToAuthItem))
		{
			// if this user assigned this role within this project
			if($ProjectToProjectTemplateToAuthItem->authAssignment->userid == Yii::app()->user->id)
			{
				 return true;
			}
		}
		
		return false;
	}
	
	public function beforeSave() {
		if(!empty($this->project_template_id))
		{
			$projectTemplate = ProjectTemplate::model()->findByPk($this->project_template_id);
		}
		$this->client_id = $projectTemplate->client_id;
		
		return parent::beforeSave();
	}
	
	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		// get the planning model
		$planning = Planning::model()->findByPk($this->id);
		$planning->name = $this->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		// atempt save
		$saved = $planning->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $planning;

		return $saved & parent::updateSave($models);
	}

	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		// need to insert a row into the planning nested set model so that the id can be used here

		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$planning = new Planning;
		$planning->name = $this->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		if($saved = $planning->saveNode(true))
		{
			// add the Project
			$this->id = $planning->id;
			$saved = parent::createSave($models);

			// add a Day
			$day = new Day;
			$day->project_id = $this->id;
			$saved = $day->createSave($models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;

		return $saved;
	}
	
}
?>