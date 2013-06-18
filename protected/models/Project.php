<?php

/**
 * This is the model class for table "tbl_project".
 *
 * The followings are the available columns in table 'tbl_project':
 * @property string $id
 * @property string $level
 * @property integer $project_type_id
 * @property integer $client_id
 * @property string $travel_time_1_way
 * @property string $critical_completion
 * @property string $planned
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Day[] $days
 * @property User $updatedBy
 * @property Planning $level0
 * @property Planning $id0
 * @property ProjectType $projectType
 * @property ProjectType $client
 * @property ProjectToAuthItem[] $projectToAuthItems
 * @property ProjectToClientContact[] $projectToClientContacts
 * @property ProjectToCustomFieldToProjectTemplate[] $projectToCustomFieldToProjectTemplates
 * @property Task[] $tasks
 */
class Project extends CustomFieldExtensionActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	public $searchInCharge;
	public $name;
	public $in_charge_id;
	public $project_template_id;
	public $projectTemplate;


// TODO: replace with trigger after insert on model. Also cascade delete on these 3 tables
// Also update triggers possibly to maintain ref integ. easiest for now in application code but not great for integrity.
	/**
	 * Creates the rows needed for generisizm.
	 * @param CActiveRecord $model the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	protected function createCustomFields(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all customValue model types associated to this models model type
		foreach($this->projectType->projectTemplate->customFieldToProjectTemplates as $customFieldModelTemplate)
		{
			// create a new customValue item to hold value
			if($saved &= CustomValue::createCustomField($customFieldModelTemplate, $models, $customValue))
			{
				// create new modelToCustomFieldModelTemplate
				$modelToCustomFieldModelTemplate = new ProjectToCustomFieldToProjectTemplate();
				$modelToCustomFieldModelTemplate->custom_field_to_project_template_id = $customFieldModelTemplate->id;
				$modelToCustomFieldModelTemplate->project_id = $this->id;
				$modelToCustomFieldModelTemplate->custom_value_id = $customValue->id;
				// attempt save
				$saved &= $modelToCustomFieldModelTemplate->dbCallback('save');
				// record any errors
				$models[] = $modelToCustomFieldModelTemplate;
			}
			else
			{//<input id="CustomField_2_type_int" class="span5" type="text" name="CustomValue[2][type_int]">
				$t = $customValue->getErrors();
			}
		}
		
		return $saved;
	}

	/**
	 * Updates the rows needed for generisizm.
	 * @param CActiveRecord $model the model
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	public function updateCustomFields(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all customValue model types associated to this models model type
		foreach($this->projectToCustomFieldToProjectTemplates as $modelToCustomFieldModelTemplate)
		{
			$customValue = $modelToCustomFieldModelTemplate->customValue;
			$customFieldModelTemplate = $modelToCustomFieldModelTemplate->customFieldToProjectTemplate;
			$customValue->setLabelAndId($customFieldModelTemplate);
			
			// massive assignement
			$customValue->attributes=$_POST['CustomValue'][$modelToCustomFieldModelTemplate->custom_value_id];

			// validate and save
			$saved &= $customValue->updateSave($models);
			//<input id="CustomField_2_type_int" class="span5" type="text" name="CustomValue[2][type_int]">
		}

		return $saved;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('project_type_id, client_id', 'required'),
			array('project_type_id, client_id', 'numerical', 'integerOnly'=>true),
			array('id, level, in_charge_id', 'length', 'max'=>10),
			array('critical_completion, planned, client_id, name', 'safe'),
			array('travel_time_1_way', 'date', 'format'=>'H:m'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, level, searchInCharge, travel_time_1_way, critical_completion, planned, name, searchProjectType', 'safe', 'on'=>'search'),
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
            'level0' => array(self::BELONGS_TO, 'Planning', 'level'),
            'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
            'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
            'client' => array(self::BELONGS_TO, 'ProjectType', 'client_id'),
            'projectToAuthItems' => array(self::HAS_MANY, 'ProjectToAuthItem', 'project_id'),
            'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'project_id'),
            'projectToCustomFieldToProjectTemplates' => array(self::HAS_MANY, 'ProjectToCustomFieldToProjectTemplate', 'project_id'),
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
				contact.first_name,
				contact.last_name,
				contact.email
				) AS searchInCharge",
			'travel_time_1_way',
			'id0.in_charge_id AS in_charge_id',
			't.critical_completion',
			't.planned',
			't.project_type_id',	// though not displayed, needed to get id for link field
			'projectType.name AS searchProjectType',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('t.travel_time_1_way',Yii::app()->format->toMysqlTime($this->travel_time_1_way));
		$criteria->compare('t.critical_completion',Yii::app()->format->toMysqlDate($this->critical_completion));
		$criteria->compare('t.planned',Yii::app()->format->toMysqlDate($this->planned));
		$criteria->compare('projectType.name', $this->searchProjectType, true);
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
			'projectType',
			'projectType.client',
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
		$columns[] = static::linkColumn('searchProjectType', 'ProjectType', 'project_type_id');
		$columns[] = 'travel_time_1_way';
		$columns[] = 'critical_completion';
		$columns[] = 'planned';
		
		return $columns;
	}

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
		$this->project_template_id = $this->projectType->project_template_id;
		$this->projectTemplate = $this->projectType->projectTemplate;
		
		parent::afterFind();
	}

	/**
	 * check if the current user is assigned to the given role within this project context
	 * used within business rule of checkAccess by Project task
	 */
	static function checkContext($primaryKey, $role)
	{
		// if this role exists for this project
		$projectToAuthItem = ProjectToAuthItem::model()->findAllByAttributes(array('project_id'=>$primaryKey, 'auth_item_name'=>$role));
		if(!empty($projectToAuthItem))
		{
			// if this user assigned this role within this project
			if($projectToAuthItem->authAssignment->userid == Yii::app()->user->id)
			{
				 return true;
			}
		}
		
		return false;
	}
	
	public function beforeSave() {
		if(!empty($this->project_type_id))
		{
			$this->project_template_id = $this->projectType->project_template_id;
			$this->projectTemplate = $this->projectType->projectTemplate;
		}
		
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
			$saved &= parent::createSave($models);

			// add a Day
			$day = new Day;
			$day->project_id = $this->id;
			$saved &= $day->createSave($models);

			// attempt creation of default roles
			$saved &= $this->createRoles($models);
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;

		return $saved;
	}
	
	/**
	 * Creates the intial roles rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createRoles(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all customValue model types associated to this models model type
		foreach(ProjectTemplateToAuthItem::model()->findAllByAttributes(array('project_template_id'=>$this->project_template_id)) as $projectTemplateToAuthItem)
		{
			// factory method to create role
			$saved &= ProjectTemplateToAuthItem::add(
				$projectTemplateToAuthItem->auth_item_name,
				$this->id,
				$models
			);
		}
		
		return $saved;
	}
	
}
?>