<?php

/**
 * This is the model class for table "tbl_user".
 *
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id
 * @property integer $contact_id
 * @property string $password
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Action[] $actions
 * @property Assembly[] $assemblies
 * @property AssemblyGroup[] $assemblyGroups
 * @property AssemblyGroupToAssembly[] $assemblyGroupToAssemblies
 * @property AssemblyToAssemblyGroup[] $assemblyToAssemblyGroups
 * @property ClientToAssembly[] $clientToAssemblys
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups
 * @property Client[] $clients
 * @property ClientContact[] $clientContacts
 * @property Contact[] $contacts
 * @property Crew[] $crews
 * @property CustomField[] $customFields
 * @property CustomFieldProjectCategory[] $customFieldProjectCategories
 * @property CustomFieldTaskCategory[] $customFieldTaskCategories
 * @property ProjectTemplateToCustomField[] $projectTemplateToCustomFields
 * @property TaskTemplateToCustomField[] $taskTemplateToCustomFields
 * @property CustomValue[] $customValues
 * @property Day[] $days
 * @property DefaultValue[] $defaultValues
 * @property Drawing[] $drawings
 * @property Duty[] $duties
 * @property DutyData[] $dutyDatas
 * @property DutyData[] $dutyDatas1
 * @property DutyStep[] $dutySteps
 * @property DutyStepDependency[] $dutyStepDependencies
 * @property Material[] $materials
 * @property MaterialGroup[] $materialGroups
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials
 * @property ClientToMaterial[] $clientToMaterials
 * @property Planning[] $plannings
 * @property Planning[] $plannings1
 * @property Project[] $projects
 * @property ProjectTemplate[] $projectTemplates
 * @property ProjectTemplateToAuthItem[] $projectTemplateToAuthItems
 * @property ProjectToAuthItem[] $projectToAuthItems
 * @property ProjectToAuthItemToAuthAssignment[] $projectToAuthItemToAuthAssignments
 * @property ProjectToClientContact[] $projectToClientContacts
 * @property ProjectToProjectTemplateToCustomField[] $projectToProjectTemplateToCustomFields
 * @property Report[] $reports
 * @property ReportToAuthItem[] $reportToAuthItems
 * @property LabourResource[] $labourResources
 * @property LabourResourceData[] $labourResourceDatas
 * @property LabourResourceToSupplier[] $labourResourceToSuppliers
 * @property Stage[] $stages
 * @property Standard[] $standards
 * @property SubAssembly[] $subAssemblies
 * @property SubReport[] $subReports
 * @property Supplier[] $suppliers
 * @property SupplierContact[] $supplierContacts
 * @property Task[] $tasks
 * @property TaskTemplate[] $taskTemplates
 * @property TaskTemplateToAction[] $taskTemplateToActions
 * @property TaskTemplateToAssembly[] $taskTemplateToAssemblies
 * @property TaskTemplateToAssemblyGroup[] $taskTemplateToAssemblyGroups
 * @property TaskTemplateToMaterial[] $taskTemplateToMaterials
 * @property TaskTemplateToMaterialGroup[] $taskTemplateToMaterialGroups
 * @property TaskTemplateToLabourResource[] $taskTemplateToLabourResources
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups
 * @property TaskToTaskTemplateToCustomField[] $taskToTaskTemplateToCustomFields
 * @property TaskToMaterial[] $taskToMaterials
 * @property TaskToMaterialToAssemblyToMaterial[] $taskToMaterialToAssemblyToMaterials
 * @property TaskToMaterialToAssemblyToMaterialGroup[] $taskToMaterialToAssemblyToMaterialGroups
 * @property TaskToMaterialToTaskTemplateToMaterialGroup[] $taskToMaterialToTaskTemplateToMaterialGroups
 * @property TaskToLabourResource[] $taskToLabourResources
 * @property User $updatedBy
 * @property User[] $users
 * @property Contact $contact
 */
class User extends ActiveRecord
{
	static $niceName = 'User';
	
	// search variables
	public $searchFirstName;
	public $searchLastName;
	public $searchPhoneMobile;
	public $searchEmail;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'actions' => array(self::HAS_MANY, 'Action', 'updated_by'),
            'assemblies' => array(self::HAS_MANY, 'Assembly', 'updated_by'),
            'assemblyGroups' => array(self::HAS_MANY, 'AssemblyGroup', 'updated_by'),
            'assemblyGroupToAssemblies' => array(self::HAS_MANY, 'AssemblyGroupToAssembly', 'updated_by'),
            'assemblyToAssemblyGroups' => array(self::HAS_MANY, 'AssemblyToAssemblyGroup', 'updated_by'),
            'clientToAssemblys' => array(self::HAS_MANY, 'ClientToAssembly', 'updated_by'),
            'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'updated_by'),
            'assemblyToMaterialGroups' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'updated_by'),
            'clients' => array(self::HAS_MANY, 'Client', 'updated_by'),
            'clientContacts' => array(self::HAS_MANY, 'ClientContact', 'updated_by'),
            'contacts' => array(self::HAS_MANY, 'Contact', 'updated_by'),
            'crews' => array(self::HAS_MANY, 'Crew', 'updated_by'),
            'customFields' => array(self::HAS_MANY, 'CustomField', 'updated_by'),
            'customFieldProjectCategories' => array(self::HAS_MANY, 'CustomFieldProjectCategory', 'updated_by'),
            'customFieldTaskCategories' => array(self::HAS_MANY, 'CustomFieldTaskCategory', 'updated_by'),
            'projectTemplateToCustomFields' => array(self::HAS_MANY, 'ProjectTemplateToCustomField', 'updated_by'),
            'taskTemplateToCustomFields' => array(self::HAS_MANY, 'TaskTemplateToCustomField', 'updated_by'),
            'customValues' => array(self::HAS_MANY, 'CustomValue', 'updated_by'),
            'days' => array(self::HAS_MANY, 'Day', 'updated_by'),
            'defaultValues' => array(self::HAS_MANY, 'DefaultValue', 'updated_by'),
            'drawings' => array(self::HAS_MANY, 'Drawing', 'updated_by'),
            'duties' => array(self::HAS_MANY, 'Duty', 'updated_by'),
            'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'updated_by'),
            'dutyDatas1' => array(self::HAS_MANY, 'DutyData', 'responsible'),
            'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'updated_by'),
            'dutyStepDependencies' => array(self::HAS_MANY, 'DutyStepDependency', 'updated_by'),
            'materials' => array(self::HAS_MANY, 'Material', 'updated_by'),
            'materialGroups' => array(self::HAS_MANY, 'MaterialGroup', 'updated_by'),
            'materialGroupToMaterials' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'updated_by'),
            'clientToMaterials' => array(self::HAS_MANY, 'ClientToMaterial', 'updated_by'),
            'plannings' => array(self::HAS_MANY, 'Planning', 'updated_by'),
            'plannings1' => array(self::HAS_MANY, 'Planning', 'in_charge_id'),
            'projects' => array(self::HAS_MANY, 'Project', 'updated_by'),
            'projectTemplates' => array(self::HAS_MANY, 'ProjectTemplate', 'updated_by'),
            'projectTemplateToAuthItems' => array(self::HAS_MANY, 'ProjectTemplateToAuthItem', 'updated_by'),
            'projectToAuthItems' => array(self::HAS_MANY, 'ProjectToAuthItem', 'updated_by'),
            'projectToAuthItemToAuthAssignments' => array(self::HAS_MANY, 'ProjectToAuthItemToAuthAssignment', 'updated_by'),
            'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'updated_by'),
            'projectToProjectTemplateToCustomFields' => array(self::HAS_MANY, 'ProjectToProjectTemplateToCustomField', 'updated_by'),
            'reports' => array(self::HAS_MANY, 'Report', 'updated_by'),
            'reportToAuthItems' => array(self::HAS_MANY, 'ReportToAuthItem', 'updated_by'),
            'resources' => array(self::HAS_MANY, 'LabourResource', 'updated_by'),
            'resourceDatas' => array(self::HAS_MANY, 'LabourResourceData', 'updated_by'),
            'resourceToSuppliers' => array(self::HAS_MANY, 'LabourResourceToSupplier', 'updated_by'),
            'stages' => array(self::HAS_MANY, 'Stage', 'updated_by'),
            'standards' => array(self::HAS_MANY, 'Standard', 'updated_by'),
            'subAssemblies' => array(self::HAS_MANY, 'SubAssembly', 'updated_by'),
            'subReports' => array(self::HAS_MANY, 'SubReport', 'updated_by'),
            'suppliers' => array(self::HAS_MANY, 'Supplier', 'updated_by'),
            'supplierContacts' => array(self::HAS_MANY, 'SupplierContact', 'updated_by'),
            'tasks' => array(self::HAS_MANY, 'Task', 'updated_by'),
            'taskTemplates' => array(self::HAS_MANY, 'TaskTemplate', 'updated_by'),
            'taskTemplateToActions' => array(self::HAS_MANY, 'TaskTemplateToAction', 'updated_by'),
            'taskTemplateToAssemblies' => array(self::HAS_MANY, 'TaskTemplateToAssembly', 'updated_by'),
            'taskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskTemplateToAssemblyGroup', 'updated_by'),
            'taskTemplateToMaterials' => array(self::HAS_MANY, 'TaskTemplateToMaterial', 'updated_by'),
            'taskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskTemplateToMaterialGroup', 'updated_by'),
            'taskTemplateToLabourResources' => array(self::HAS_MANY, 'TaskTemplateToLabourResource', 'updated_by'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'updated_by'),
            'taskToAssemblyToAssemblyToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'updated_by'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'updated_by'),
            'taskToTaskTemplateToCustomFields' => array(self::HAS_MANY, 'TaskToTaskTemplateToCustomField', 'updated_by'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'updated_by'),
            'taskToMaterialToAssemblyToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterial', 'updated_by'),
            'taskToMaterialToAssemblyToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'updated_by'),
            'taskToMaterialToTaskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'updated_by'),
            'taskToLabourResources' => array(self::HAS_MANY, 'TaskToLabourResource', 'updated_by'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'users' => array(self::HAS_MANY, 'User', 'updated_by'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchFirstName', $this->searchFirstName, 'contact.first_name', true);
		$criteria->compareAs('searchLastName', $this->searchLastName, 'contact.last_name', true);
		$criteria->compareAs('searchPhoneMobile', $this->searchPhoneMobile, 'contact.phone_mobile', true);
		$criteria->compareAs('searchEmail', $this->searchEmail, 'contact.email', true);

		// with
		$criteria->with=array(
			'contact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchFirstName';
		$columns[] = 'searchLastName';
        $columns[] = array(
			'name'=>'searchEmail',
			'value'=>'$data->searchEmail',
			'type'=>'email',
		);
		$columns[] = array(
			'name'=>'searchPhoneMobile',
			'value'=>'CHtml::link($data->searchPhoneMobile, "tel:".$data->searchPhoneMobile)',
			'type'=>'raw',
		);
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchFirstName',
			'searchLastName',
			'searchPhoneMobile',
			'searchEmail',
		);
	}

	/**
	 * perform one-way encryption on the password before we standard it in the database
	 */
	protected function afterValidate()
	{   
		parent::afterValidate();
		$this->password = $this->encrypt($this->password);                     
	}
	
	public function encrypt($value)
	{
		// if the password value has changed
		return $this->isNewRecord || $this->attributeChanged('password') ? md5($value) : $value;
	}
	
	
}

?>