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
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Day[] $days
 * @property User $updatedBy
 * @property Planning $level
 * @property Planning $id0
 * @property ProjectType $projectType
 * @property ProjectType $client
 * @property ProjectToAuthItem[] $projectToAuthItems
 * @property ProjectToClientContact[] $projectToClientContacts
 * @property ProjectToProjectTemplateToCustomField[] $projectToProjectTemplateToCustomFields
 * @property Task[] $tasks
 */
class Project extends CustomFieldActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	public $critical_completion;
	public $name;
	public $in_charge_id;
	public $project_template_id;
	public $projectTemplate;

	// CustomFieldActiveRecord
	protected $evalCustomFieldPivots = '$this->projectTemplate->projectTemplateToCustomFields';
	protected $evalClassEndToCustomFieldPivot = 'ProjectToProjectTemplateToCustomField';
	protected $evalColumnCustomFieldModelTemplateId = 'project_template_to_custom_field_id';
	protected $evalColumnEndId = 'project_id';
	protected $evalEndToCustomFieldPivots = '$this->projectToProjectTemplateToCustomFields';
	protected $evalCustomFieldPivot = 'projectTemplateToCustomField';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(), array(
			array('in_charge_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('critical_completion', 'safe'),
		));
	}

	public function tableName() {

		// need to create a single shot instance of creating the temp table that appends required custom columns - only if in search scenario will actually
		// do the search later when attribute assignments have been made which will repeat this - however some methods need the table architecture earlier
		static $called = false;

		if(!$called && $this->scenario == 'search')
		{
			Yii::app()->db->createCommand("CALL pro_get_projects_from_client_admin_view({$_GET['client_id']})")->execute();
			$called = true;
		}

		return ($this->scenario == 'search') || static::$inSearch
			? 'tmp_table'
			: 'tbl_project';
	}
	
	/**
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
            'level' => array(self::BELONGS_TO, 'Planning', 'level'),
            'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
            'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
            'client' => array(self::BELONGS_TO, 'ProjectType', 'client_id'),
            'projectToAuthItems' => array(self::HAS_MANY, 'ProjectToAuthItem', 'project_id'),
            'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'project_id'),
            'projectToProjectTemplateToCustomFields' => array(self::HAS_MANY, 'ProjectToProjectTemplateToCustomField', 'project_id'),
            'tasks' => array(self::HAS_MANY, 'Task', 'project_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'derived_in_charge' => 'In charge',
			'name' => 'Project',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria = new DbCriteria;

		$criteria->compareAs('searchProjectType', $this->searchProjectType, 'projectType.name', true);

		$criteria->with = array(
			'projectType',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns['id'] = 'id';
		$columns['name'] = 'name';
		$columns['critical_completion'] = 'critical_completion';
        $columns['derived_in_charge'] = static::linkColumn('derived_in_charge', 'User', 'in_charge_id');
		$columns['searchProjectType'] = 'searchProjectType';
		$columns['travel_time_1_way'] = 'travel_time_1_way';
		
		// loop thru temporary table columns
		$isCustom = FALSE;
		foreach(static::model()->tableSchema->getColumnNames() AS $key => $tempTableColumnName)
		{
			// start from the last fixed column
			if($tempTableColumnName == 'derived_project_template_description')
			{
				$isCustom = TRUE;
				continue;
			}
			elseif($isCustom === FALSE)
			{
				continue;
			}

			// if not already in our list of columns to show
			if(!array_key_exists($tempTableColumnName, $columns))
			{
				// use setter to dynamically create an attribute
				$columns[] = "$tempTableColumnName::" . str_replace('_', ' ', $tempTableColumnName);
			}
		}

		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displayAttr[]='name';

		return $displayAttr;
	}

	public function afterFind() {
		$this->name = $this->id0->name;
		$this->critical_completion = $this->id0->critical_completion;
		$this->project_template_id = $this->projectType->project_template_id;
		$this->projectTemplate = $this->projectType->projectTemplate;
		
		parent::afterFind();
	}

	/**
	 * check if the current user is assigned to the given role within this project context
	 */
	static function checkContext($primaryKey, $role)
	{
		// if this role exists for this project
		if($projectToAuthItem = ProjectToAuthItem::model()->findAllByAttributes(array('project_id'=>$primaryKey, 'auth_item_name'=>$role)))
		{
			// if this user assigned this role within this project
			foreach($projectToAuthItem->projectToAuthItemToAuthAssignments as $projectToAuthItemToAuthAssignment)
			{
				if($projectToAuthItemToAuthAssignment->authAssignment->userid == Yii::app()->user->id)
				{
					 return true;
				}
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
		$planning->critical_completion = $this->critical_completion;
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
	public function createSave(&$models=array(), $runValidation = true)
	{
		// need to insert a row into the planning nested set model so that the id can be used here

		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$planning = new Planning;
		$planning->name = $this->name;
		$planning->critical_completion = $this->critical_completion;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];
		if($saved = $planning->saveNode(true))
		{
			// add the Project
			$this->id = $planning->id;
			$this->level = $planning->level;
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