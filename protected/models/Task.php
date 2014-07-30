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
 * @property string $crew_id
 * @property integer $mode_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Duty[] $duties
 * @property Project $project
 * @property User $updatedBy
 * @property TaskTemplate $taskTemplate
 * @property Planning $level
 * @property Crew $crew
 * @property Planning $id0
 * @property Mode $mode
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToTaskTemplateToCustomField[] $taskToTaskTemplateToCustomFields
 * @property TaskToMaterial[] $taskToMaterials
 * @property TaskToLabourResource[] $taskToLabourResources
 */
class Task extends CustomFieldActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $critical_completion;
	public $name;
	public $in_charge_id;
	
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
	public function rules($ignores = array())
	{
		return array_merge(parent::rules(), array(
			array('in_charge_id, ', 'numerical', 'integerOnly'=>true),
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
            'level' => array(self::BELONGS_TO, 'Planning', 'level'),
            'crew' => array(self::BELONGS_TO, 'Crew', 'crew_id'),
            'id0' => array(self::BELONGS_TO, 'Planning', 'id'),
            'mode' => array(self::BELONGS_TO, 'Mode', 'mode_id'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'task_id'),
            'taskToTaskTemplateToCustomFields' => array(self::HAS_MANY, 'TaskToTaskTemplateToCustomField', 'task_id'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'task_id'),
            'taskToLabourResources' => array(self::HAS_MANY, 'TaskToLabourResource', 'task_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array())
	{
		return parent::attributeLabels(array(
			'derived_in_charge' => 'In charge',
			'derived_task_template_description' => 'Template',
			'name' => 'Task',
			'derived_earliest' => 'Earliest',
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
		$dataProvider = new CustomFieldActiveDataProvider($this, array(
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
		$columns['critical_completion'] = 'critical_completion';
		$columns['quantity'] = 'quantity';
        $columns['derived_in_charge'] = static::linkColumn('derived_in_charge', 'User', 'in_charge_id');
        $columns['derived_task_template_description'] = static::linkColumn('derived_task_template_description', 'TaskTemplate', 'task_template_id');
		$columns['derived_earliest'] = 'derived_earliest:date';
		
		// loop thru temporary table columns
		$isCustom = FALSE;
		foreach(static::model()->tableSchema->getColumnNames() AS $key => $tempTableColumnName)
		{
			// start from the last fixed column
			if($tempTableColumnName == 'derived_task_template_description')
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
				$label = 'test';
				
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
		$displaAttr[]='id';
		$displaAttr[]='name';

		return $displaAttr;
	}

	public function afterFind() {

		$this->name = $this->id0->name;
		$this->in_charge_id = $this->id0->in_charge_id;
		$this->critical_completion = $this->id0->critical_completion;
	
		parent::afterFind();
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
	public function assertFromParent($modelName = NULL)
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
	public function createSave(&$models=array(), $runValidation=true)
	{
		// need to insert a row into the planning nested set model so that the id can be used here
		
		// create a root node
		// NB: the project description is actually the name field in the nested set model
		$planning = new Planning;
		$planning->name = $this->name;
		$planning->critical_completion = $this->critical_completion;
		$planning->in_charge_id = empty($_POST['Planning']['in_charge_id']) ? null : $_POST['Planning']['in_charge_id'];

		if($saved = $planning->appendTo(Planning::model()->findByPk($this->crew_id)))
		{
			$this->id = $planning->id;
			$this->quantity = $this->taskTemplate->quantity;
			// parent create save will add customValues -- all we need to do is take care care of adding the other things if no errors
			// NB: by calling the parent this is added into $models
			if($saved = parent::createSave($models, $runValidation))
			{
				// attempt creation of resources - only if mode set
				if($this->mode_id)
				{
					$saved &= $this->createLabourResources($models);
					$saved &= $this->createPlant($models);
				}
				// attempt creation of assemblies
				$saved &= $this->createAssemblies($models);
				// attempt creation of materials
				$saved &= $this->createMaterials($models);
				// attempt creation of assembly groups
				$saved &= $this->createAssemblyGroups($models);
				// attempt creation of material groups
				$saved &= $this->createMaterialGroups($models);
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
	 * Creates the intial labourResource rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createLabourResources(&$models=array())
	{
		$saved = true;

		foreach($this->taskTemplate->taskTemplateToLabourResources as $taskTemplateToLabourResource)
		{
			// create a new labourResource
			$taskToLabourResource = new TaskToLabourResource();
			// copy any useful attributes from
			$taskToLabourResource->attributes = $taskTemplateToLabourResource->attributes;
			$taskToLabourResource->updated_by = null;
			$taskToLabourResource->task_id = $this->id;
			$saved &= $taskToLabourResource->createSave($models, $taskTemplateToLabourResource);
		}

		// Adding exclusives has to been done after as the child records may not exist until the above loop has been processed
		foreach($this->taskTemplate->taskTemplateToLabourResources as $taskTemplateToLabourResource)
		{
			$criteria = new DbCriteria;
			$criteria->with = 'labourResourceData';
			$criteria->compare('task_id',$this->id);
			$criteria->compare('labourResourceData.labour_resource_id',$taskTemplateToLabourResource->labour_resource_id);
			$criteria->compare('labourResourceData.mode_id',$taskTemplateToLabourResource->mode_id);
			$criteria->compare('labourResourceData.level',$taskTemplateToLabourResource->level);
			// find the corresponding task to labour resource record - will be the parent
			$taskToLabourResourceParent = TaskToLabourResource::model()->find($criteria);

			// loop thru template children exlusives
			foreach($taskTemplateToLabourResource->taskTemplateToMutuallyExclusiveRoles1 as $taskTemplateToExlusiveRoleChild)
			{
				$criteria = new DbCriteria;
				$criteria->with = 'labourResourceData';
				$criteria->compare('task_id',$this->id);
				$criteria->compare('labourResourceData.labour_resource_id',$taskTemplateToExlusiveRoleChild->child->labour_resource_id);
				$criteria->compare('labourResourceData.mode_id',$taskTemplateToExlusiveRoleChild->child->mode_id);
				$criteria->compare('labourResourceData.level',$taskTemplateToExlusiveRoleChild->child->level);
				// we have the parent above but still need to find the child in the same way
				$taskToLabourResourceChild = TaskToLabourResource::model()->find($criteria);
				$mutuallyExclusiveRole = new MutuallyExclusiveRole;
				$mutuallyExclusiveRole->parent_id = $taskToLabourResourceParent->labour_resource_data_id;
				$mutuallyExclusiveRole->child_id = $taskToLabourResourceChild->labour_resource_data_id;
				$mutuallyExclusiveRole->planning_id = $taskToLabourResourceChild->labourResourceData->planning_id;
				try
				{
					$mutuallyExclusiveRole->insert();
				}
				catch(CDbException $e)
				{
					// ignore duplicates - need try and catch as no insert ignore support yii
				}
			}
		}
		
		return $saved;
	}

	/**
	 * Creates the intial plant rows for a task
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createPlant(&$models=array())
	{
		$saved = true;

		foreach($this->taskTemplate->taskTemplateToPlants as $taskTemplateToPlant)
		{
			// create a new plant
			$taskToPlant = new TaskToPlant();
			// copy any useful attributes from
			$taskToPlant->attributes = $taskTemplateToPlant->attributes;
			$taskToPlant->updated_by = null;
			$taskToPlant->task_id = $this->id;
			$saved &= $taskToPlant->createSave($models, $taskTemplateToPlant);
			
			// add capabilities
			foreach($taskTemplateToPlant->taskTemplateToPlantCapabilities as $taskTemplateToPlantCapability)
			{
				$plantDataToPlantCapability = new PlantDataToPlantCapability;
				$plantDataToPlantCapability->plant_data_id = $taskToPlant->plant_data_id;
				$plantDataToPlantCapability->plant_capability_id = $taskTemplateToPlantCapability->plant_capability_id;
				$plantDataToPlantCapability->plant_to_supplier_id = $taskTemplateToPlantCapability->plant_to_supplier_id;
				$plantDataToPlantCapability->quantity = $taskTemplateToPlantCapability->quantity;

				try
				{
					$plantDataToPlantCapability->insert();
				}
				catch(CDbException $e)
				{
					// ignore duplicates - need try and catch as no insert ignore support yii
				}
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
	 * Append assembly groups to task.
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createAssemblyGroups(&$models=array())
	{
		// initialise the saved variable to show no errors
		$saved = true;

		// loop thru all all assemblies related to the tasks type
		foreach($this->taskTemplate->taskTemplateToAssemblyGroups as $taskTemplateToAssemblyGroup)
		{
			$taskToAssemblyToTaskTemplateToAssemblyGroup = new TaskToAssemblyToTaskTemplateToAssemblyGroup();
			$taskToAssemblyToTaskTemplateToAssemblyGroup->task_id = $this->id;
			$taskToAssemblyToTaskTemplateToAssemblyGroup->quantity = 0;	// dummy
			$taskToAssemblyToTaskTemplateToAssemblyGroup->assembly_group_id = $taskTemplateToAssemblyGroup->assembly_group_id;
			$taskToAssemblyToTaskTemplateToAssemblyGroup->task_template_to_assembly_group_id = $taskTemplateToAssemblyGroup->id;
			$taskToAssemblyToTaskTemplateToAssemblyGroup->createSave($models);
		}
		
		return $saved;
	} 
	
	/**
	 * Append material groups to task.
	 * @param CActiveRecord $model the model (task)
	 * @param array of CActiveRecord models to extract errors from if necassary
	 * @return returns 0, or null on error of any inserts
	 */
	private function createMaterialGroups(&$models=array())
	{
		// initialise the saved variable to show no errors
		$saved = true;

		// loop thru all all assemblies related to the tasks type
		foreach($this->taskTemplate->taskTemplateToMaterialGroups as $taskTemplateToMaterialGroup)
		{
			$taskToMaterialToTaskTemplateToMaterialGroup = new TaskToMaterialToTaskTemplateToMaterialGroup();
			$taskToMaterialToTaskTemplateToMaterialGroup->task_id = $this->id;
			$taskToMaterialToTaskTemplateToMaterialGroup->quantity = 0;	// dummy
			$taskToMaterialToTaskTemplateToMaterialGroup->material_group_id = $taskTemplateToMaterialGroup->material_group_id;
			$taskToMaterialToTaskTemplateToMaterialGroup->task_template_to_material_group_id = $taskTemplateToMaterialGroup->id;
			$taskToMaterialToTaskTemplateToMaterialGroup->createSave($models);
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