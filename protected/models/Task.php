<?php

/**
 * This is the model class for table "tbl_task".
 *
 * The followings are the available columns in table 'tbl_task':
 * @property string $id
 * @property string $level
 * @property string $project_id
 * @property integer $task_template_id
 * @property integer $quantity
 * @property string $planned
 * @property string $location
 * @property integer $preferred_mon
 * @property integer $preferred_tue
 * @property integer $preferred_wed
 * @property integer $preferred_thu
 * @property integer $preferred_fri
 * @property integer $preferred_sat
 * @property integer $preferred_sun
 * @property string $crew_id
 * @property integer $mode_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property Project $project
 * @property User $updatedBy
 * @property TaskTemplate $taskTemplate
 * @property Planning $level0
 * @property Crew $crew
 * @property Planning $id0
 * @property Mode $mode
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToTaskTemplateToCustomField[] $taskToTaskTemplateToCustomFields
 * @property TaskToMaterial[] $taskToMaterials
 * @property TaskToHumanResource[] $taskToHumanResources
 */
class Task extends CustomFieldActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchInCharge;
	public $searchProject;
	public $searchTaskTemplate;
	public $searchEarliest;
	public $name;
	public $in_charge_id;
	/**
	 * inline checkbox property 
	 */
	public $preferred = array();
	
	// CustomFieldActiveRecord
	protected $evalCustomFieldPivots = '$this->taskTemplate->taskTemplateToCustomFields';
	protected $evalClassEndToCustomFieldPivot = 'TaskToTaskTemplateToCustomField';
	protected $evalColumnCustomFieldModelTemplateId = 'task_template_to_custom_field_id';
	protected $evalColumnEndId = 'task_id';
	protected $evalEndToCustomFieldPivots = '$this->taskToTaskTemplateToCustomFields';
	protected $evalCustomFieldPivot = 'taskTemplateToCustomField';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(), array(
			array('in_charge_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
		));
	}

	// needed as using a view to concat custom columns in admin view
	public function primaryKey()
	{
		return 'id';
	}

	public function tableName() {

		// need to create a single shot instance of creating the temp table that appends required custom columns - only if in search scenario will actually
		// do the search later when attribute assignments have been made which will repeat this - however some methods need the table architecture earlier
		static $called = false;

		if(!$called && $this->scenario == 'search')
		{
			Yii::app()->db->createCommand("CALL pro_get_tasks_from_planning_admin_view({$_GET['crew_id']})")->execute();
			$called = true;
		}

		return ($this->scenario == 'search') || static::$inSearch
			? 'tmp_table'
			: 'tbl_task';
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'duties' => array(self::HAS_MANY, 'Duty', 'task_id'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskTemplate' => array(self::BELONGS_TO, 'TaskTemplate', 'task_template_id'),
            'level0' => array(self::BELONGS_TO, 'Planning', 'level'),
            'crew' => array(self::BELONGS_TO, 'Crew', 'crew_id'),
            'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'task_id'),
            'taskToTaskTemplateToCustomFields' => array(self::HAS_MANY, 'TaskToTaskTemplateToCustomField', 'task_id'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'task_id'),
            'taskToHumanResources' => array(self::HAS_MANY, 'TaskToHumanResource', 'task_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'derived_in_charge' => 'In charge',
			'derived_task_template_description' => 'Template',
			'name' => 'Task',
			'derived_earliest' => 'Earliest',
			'preferred_mon' => 'Mo',
			'preferred_tue' => 'Tu',
			'preferred_wed' => 'We',
			'preferred_thu' => 'Th',
			'preferred_fri' => 'Fr',
			'preferred_sat' => 'Sa',
			'preferred_sun' => 'Su',
		));
	}

	/**
	 * Different becuase fo the temp table and need the extra columns
	 */
	public function search($pagination = array())
	{
		// get the sort order
		foreach($this->adminColumns as $adminColumn)
		{
			if(is_array($adminColumn))
			{
				if(isset($adminColumn['name']))
				{
					$attribute = $adminColumn['name'];
				}
				else
				{
					continue;;
				}
			}
			else
			{
				$attribute = $adminColumn;
			}

			$attribute = preg_replace('/:.*/', '', $attribute);
			$sort[$attribute] = array(
						'asc'=>" $attribute ",
						'desc'=>" $attribute DESC",
					);
		}
		
		// add all other attributes
		$sort[] = '*';
		
		// use custom made ActiveDataProvider just for this purpose
		$dataProvider = new TaskActiveDataProvider($this, array(
			'criteria'=>self::getSearchCriteria($this),
			'sort'=>array('attributes'=>$sort),
			'pagination' => $pagination,
		));
	
		return $dataProvider;
	}

	public function getAdminColumns()
	{
		$columns['id'] = 'id';
		$columns['name'] = 'name';
		$columns['quantity'] = 'quantity';
		$columns['location'] = 'location';
        $columns['derived_in_charge'] = static::linkColumn('derived_in_charge', 'User', 'in_charge_id');
        $columns['derived_task_template_description'] = static::linkColumn('derived_task_template_description', 'TaskTemplate', 'task_template_id');
		$columns['planned'] = 'planned';
		$columns['derived_earliest'] = 'derived_earliest:date';
		
		// loop thru temporary table columns
		$isCustom = FALSE;
		foreach(static::model()->tableSchema->getColumnNames() AS $key => $tempTableColumnName)
		{
			// start from derived_planned - the last fixed column
			if($tempTableColumnName == 'derived_planned')
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
				$taskTemplateToCustomField = TaskTemplateToCustomField::model()->findByPk(str_replace('task_template_to_custom_field_id_', '', $tempTableColumnName));
				$label = $taskTemplateToCustomField->label_override
					? $taskTemplateToCustomField->label_override
					: $taskTemplateToCustomField->customField->label;
				
				// use setter to dynamically create an attribute
				$columns[] = "$tempTableColumnName::$label";
			}
		}

		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='id';
		$displaAttr[]='name';

		return $displaAttr;
	}

	public function beforeSave() {

		if(!empty($this->preferred))
		{
			$this->preferred_mon = in_array('0', $this->preferred);
			$this->preferred_tue = in_array('1', $this->preferred);
			$this->preferred_wed = in_array('2', $this->preferred);
			$this->preferred_thu = in_array('3', $this->preferred);
			$this->preferred_fri = in_array('4', $this->preferred);
			$this->preferred_sat = in_array('5', $this->preferred);
			$this->preferred_sun = in_array('6', $this->preferred);
		}

		return parent::beforeSave();
	}

	public function afterFind() {
		// prepare check box row items
		if($this->preferred_mon)
		{
			$this->preferred[] = 0;
		}
		if($this->preferred_tue)
		{
			$this->preferred[] = 1;
		}
		if($this->preferred_wed)
		{
			$this->preferred[] = 2;
		}
		if($this->preferred_thu)
		{
			$this->preferred[] = 3;
		}
		if($this->preferred_fri)
		{
			$this->preferred[] = 4;
		}
		if($this->preferred_sat)
		{
			$this->preferred[] = 5;
		}
		if($this->preferred_sun)
		{
			$this->preferred[] = 6;
		}

		$this->name = $this->id0->name;
		$this->in_charge_id = $this->id0->in_charge_id;
	
		parent::afterFind();
	}
	
	/*
	 * can't set default value in database as TEXT data type but is required
	 */
	public function init()
	{
		// can't set default value in database as TEXT data type for AuthItem
		$this->planned = date('d M, Y');
		
		parent::init();
	}

	/*
	 * can't set default value in database as TEXT data type but is required
	 */
	public function beforeValidate()
	{
// TODO: possibly may not need project_id in task unless circular constraint
		$crew = Crew::model()->findByPk($this->crew_id);
		$this->project_id = $crew->day->project_id;
		
		return parent::beforeValidate();
	}

	// ensure that where possible a pk has been passed from parent
	// needed to overwrite this here because project has to look thru project type to get to client when doing update but gets client for admin
	public function assertFromParent()
	{
		// if update in planning view
		if(isset($_POST['controller']['Planning']) && isset($_GET['project_id']))
		{
			// ensure that that at least the parents primary key is set for the admin view of planning
			Controller::setAdminParam('project_id', $_GET['project_id'], 'Planning');
		}
		
		// if we are in the schdule screen then they may not be a parent foreign key as will be derived when user identifies a node
		if(!(Yii::app()->controller->id == 'planning'))
		{
			return parent::assertFromParent();
		}
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
	public function createSave(&$models=array(), $runValidation=true)
	{
		// need to insert a row into the planning nested set model so that the id can be used here
		
		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$planning = new Planning;
		$planning->name = $this->name;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];

		if($saved = $planning->appendTo(Planning::model()->findByPk($this->crew_id)))
		{
			$this->id = $planning->id;
			$this->quantity = $this->taskTemplate->quantity;
			// parent create save will add customValues -- all we need to do is take care care of adding the other things if no errors
			// NB: by calling the parent this is added into $models
			if($saved = parent::createSave($models, $runValidation))
			{
				// attempt creation of resources
				$saved &= $this->createHumanResources($models);
				// attempt creation of assemblies
				$saved &= $this->createAssemblies($models);
				// attempt creation of materials
				$saved &= $this->createMaterials($models);
				// attempt creation of duties
				$saved &= $this->createDutys($models);
			}
		}

		// put the model into the models array used for showing all errors
		$models[] = $planning;
		
		return $saved;
	}

// TODO: replace these with trigger after insert on model. Also cascade delete on these 3 tables
// Also update triggers possibly to maintain ref integ. easiest for now in application code but not great for integrity.
	
	/**
	 * Creates the intial humanResource rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createHumanResources(&$models=array())
	{
		$saved = true;

		foreach($this->taskTemplate->taskTemplateToHumanResources as $taskTemplateToHumanResource)
		{
			// create a new humanResource
			$taskToHumanResource = new TaskToHumanResource();
			// copy any useful attributes from
			$taskToHumanResource->attributes = $taskTemplateToHumanResource->attributes;
			$taskToHumanResource->updated_by = null;
			$taskToHumanResource->task_id = $this->id;
			$saved &= $taskToHumanResource->createSave($models, $taskTemplateToHumanResource);
		}

		// Adding exclusives has to been done after as the child records may not exist until the above loop has been processed
		foreach($this->taskTemplate->taskTemplateToHumanResources as $taskTemplateToHumanResource)
		{
			$criteria = new DbCriteria;
			$criteria->with = 'humanResourceData';
			$criteria->compare('task_id',$this->id);
			$criteria->compare('humanResourceData.human_resource_id',$taskTemplateToHumanResource->human_resource_id);
			$criteria->compare('humanResourceData.mode_id',$taskTemplateToHumanResource->mode_id);
			$criteria->compare('humanResourceData.level',$taskTemplateToHumanResource->level);
			// find the corresponding task to human resource record - will be the parent
			$taskToHumanResourceParent = TaskToHumanResource::model()->find($criteria);

			// loop thru template children exlusives
			foreach($taskTemplateToHumanResource->taskTemplateToExclusiveRoles1 as $taskTemplateToExlusiveRoleChild)
			{
				$criteria = new DbCriteria;
				$criteria->with = 'humanResourceData';
				$criteria->compare('task_id',$this->id);
				$criteria->compare('humanResourceData.human_resource_id',$taskTemplateToExlusiveRoleChild->child->human_resource_id);
				$criteria->compare('humanResourceData.mode_id',$taskTemplateToExlusiveRoleChild->child->mode_id);
				$criteria->compare('humanResourceData.level',$taskTemplateToExlusiveRoleChild->child->level);
				// we have the parent above but still need to find the child in the same way
				$taskToHumanResourceChild = TaskToHumanResource::model()->find($criteria);

				$exclusiveRole = new ExclusiveRole;
				$exclusiveRole->parent_id = $taskToHumanResourceParent->id;
				$exclusiveRole->child_id = $taskToHumanResourceChild->id;
				$exclusiveRole->planning_id = $planning_id;
				$saved &= $exclusiveRole->insert();
			}
		}
		
		return $saved;
	}

	/**
	 * Append assemblies to task.
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createAssemblies(&$models=array())
	{
		// initialise the saved variable to show no errors
		$saved = true;

		// loop thru all all assemblies related to the tasks type
		foreach($this->taskTemplate->taskTemplateToAssemblies as $taskTemplateToAssembly)
		{
			$saved = TaskToAssemblyController::addAssembly($this->id, $taskTemplateToAssembly->assembly_id, TaskToAssembly::getDefault($taskTemplateToAssembly), null, null, $models);
		}
		
		return $saved;
	} 
	
	/**
	 * Creates the intial material rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createMaterials(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all customValue model types associated to this models model type
		foreach($this->taskTemplate->taskTemplateToMaterials as $taskTemplateToMaterial)
		{
			// create a new materials
			$taskToMaterial = new TaskToMaterial();
			// copy any useful attributes from
			$taskToMaterial->quantity = $taskToMaterial->getDefault($taskTemplateToMaterial);
			$taskToMaterial->material_id = $taskTemplateToMaterial->material_id;
			$taskToMaterial->updated_by = null;
			$taskToMaterial->task_id = $this->id;
			// need dummy standard id to get around rules
			$taskToMaterial->standard_id = 0;
			$saved &= $taskToMaterial->createSave($models);
		}
		
		return $saved;
	}

	/**
	 * Creates the intial duty rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createDutys(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all customValue model types associated to this models model type
		foreach($this->taskTemplate->taskTemplateToActions1 as $taskTemplateToAction)
		{
			// factory method to create duties
			$saved &= Duty::addDuties($taskTemplateToAction->action_id, $this, $models);
		}
		
		return $saved;
	}
	
}
?>