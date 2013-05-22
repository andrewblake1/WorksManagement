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
 * @property Assembly[] $assemblies
 * @property AssemblyGroup[] $assemblyGroups
 * @property AssemblyGroupToAssembly[] $assemblyGroupToAssemblies
 * @property AssemblyToAssemblyGroup[] $assemblyToAssemblyGroups
 * @property AssemblyToClient[] $assemblyToClients
 * @property AssemblyToDrawing[] $assemblyToDrawings
 * @property AssemblyToMaterial[] $assemblyToMaterials
 * @property AssemblyToMaterialGroup[] $assemblyToMaterialGroups
 * @property Client[] $clients
 * @property ClientContact[] $clientContacts
 * @property Contact[] $contacts
 * @property Crew[] $crews
 * @property CustomField[] $customFields
 * @property CustomFieldProjectCategory[] $customFieldProjectCategories
 * @property CustomFieldTaskCategory[] $customFieldTaskCategories
 * @property CustomFieldToProjectTemplate[] $customFieldToProjectTemplates
 * @property CustomFieldToTaskTemplate[] $customFieldToTaskTemplates
 * @property CustomValue[] $customValues
 * @property Day[] $days
 * @property DefaultValue[] $defaultValues
 * @property Drawing[] $drawings
 * @property DrawingAdjacencyList[] $drawingAdjacencyLists
 * @property Duty[] $duties
 * @property Duty[] $duties1
 * @property DutyCategory[] $dutyCategories
 * @property DutyData[] $dutyDatas
 * @property DutyStep[] $dutySteps
 * @property Material[] $materials
 * @property MaterialGroup[] $materialGroups
 * @property MaterialGroupToMaterial[] $materialGroupToMaterials
 * @property MaterialToClient[] $materialToClients
 * @property Planning[] $plannings
 * @property Planning[] $plannings1
 * @property Project[] $projects
 * @property ProjectTemplate[] $projectTemplates
 * @property ProjectTemplateToAuthItem[] $projectTemplateToAuthItems
 * @property ProjectToClientContact[] $projectToClientContacts
 * @property ProjectToCustomFieldToProjectTemplate[] $projectToCustomFieldToProjectTemplates
 * @property ProjectToProjectTemplateToAuthItem[] $projectToProjectTemplateToAuthItems
 * @property PurchaseOrder[] $purchaseOrders
 * @property Report[] $reports
 * @property ReportToAuthItem[] $reportToAuthItems
 * @property Resource[] $resources
 * @property ResourceCategory[] $resourceCategories
 * @property ResourceData[] $resourceDatas
 * @property ResourceToSupplier[] $resourceToSuppliers
 * @property Stage[] $stages
 * @property Standard[] $standards
 * @property SubAssembly[] $subAssemblies
 * @property SubReport[] $subReports
 * @property Supplier[] $suppliers
 * @property SupplierContact[] $supplierContacts
 * @property Task[] $tasks
 * @property TaskTemplate[] $taskTemplates
 * @property TaskTemplateToAssembly[] $taskTemplateToAssemblies
 * @property TaskTemplateToAssemblyGroup[] $taskTemplateToAssemblyGroups
 * @property TaskTemplateToDutyType[] $taskTemplateToDutyTypes
 * @property TaskTemplateToMaterial[] $taskTemplateToMaterials
 * @property TaskTemplateToMaterialGroup[] $taskTemplateToMaterialGroups
 * @property TaskTemplateToResource[] $taskTemplateToResources
 * @property TaskToAssembly[] $taskToAssemblies
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups
 * @property TaskToCustomFieldToTaskTemplate[] $taskToCustomFieldToTaskTemplates
 * @property TaskToMaterial[] $taskToMaterials
 * @property TaskToMaterialToAssemblyToMaterial[] $taskToMaterialToAssemblyToMaterials
 * @property TaskToMaterialToAssemblyToMaterialGroup[] $taskToMaterialToAssemblyToMaterialGroups
 * @property TaskToMaterialToTaskTemplateToMaterialGroup[] $taskToMaterialToTaskTemplateToMaterialGroups
 * @property TaskToPurchaseOrder[] $taskToPurchaseOrders
 * @property TaskToResource[] $taskToResources
 * @property User $updatedBy
 * @property User[] $users
 * @property Contact $contact
 */
class User extends ContactActiveRecord
{
	static $niceNamePlural = 'User';

	public $first_name;
	public $last_name;
	public $phone_mobile;
	public $email;
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('first_name, last_name, email', 'required'),
			array('first_name, last_name, phone_mobile', 'length', 'max'=>64),
			array('email', 'length', 'max'=>255),
			array('password', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, first_name, last_name, phone_mobile, email', 'safe', 'on'=>'search'),
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
            'assemblies' => array(self::HAS_MANY, 'Assembly', 'updated_by'),
            'assemblyGroups' => array(self::HAS_MANY, 'AssemblyGroup', 'updated_by'),
            'assemblyGroupToAssemblies' => array(self::HAS_MANY, 'AssemblyGroupToAssembly', 'updated_by'),
            'assemblyToAssemblyGroups' => array(self::HAS_MANY, 'AssemblyToAssemblyGroup', 'updated_by'),
            'assemblyToClients' => array(self::HAS_MANY, 'AssemblyToClient', 'updated_by'),
            'assemblyToDrawings' => array(self::HAS_MANY, 'AssemblyToDrawing', 'updated_by'),
            'assemblyToMaterials' => array(self::HAS_MANY, 'AssemblyToMaterial', 'updated_by'),
            'assemblyToMaterialGroups' => array(self::HAS_MANY, 'AssemblyToMaterialGroup', 'updated_by'),
            'clients' => array(self::HAS_MANY, 'Client', 'updated_by'),
            'clientContacts' => array(self::HAS_MANY, 'ClientContact', 'updated_by'),
            'contacts' => array(self::HAS_MANY, 'Contact', 'updated_by'),
            'crews' => array(self::HAS_MANY, 'Crew', 'updated_by'),
            'customFields' => array(self::HAS_MANY, 'CustomField', 'updated_by'),
            'customFieldProjectCategories' => array(self::HAS_MANY, 'CustomFieldProjectCategory', 'updated_by'),
            'customFieldTaskCategories' => array(self::HAS_MANY, 'CustomFieldTaskCategory', 'updated_by'),
            'customFieldToProjectTemplates' => array(self::HAS_MANY, 'CustomFieldToProjectTemplate', 'updated_by'),
            'customFieldToTaskTemplates' => array(self::HAS_MANY, 'CustomFieldToTaskTemplate', 'updated_by'),
            'customValues' => array(self::HAS_MANY, 'CustomValue', 'updated_by'),
            'days' => array(self::HAS_MANY, 'Day', 'updated_by'),
            'defaultValues' => array(self::HAS_MANY, 'DefaultValue', 'updated_by'),
            'drawings' => array(self::HAS_MANY, 'Drawing', 'updated_by'),
            'drawingAdjacencyLists' => array(self::HAS_MANY, 'DrawingAdjacencyList', 'updated_by'),
            'duties' => array(self::HAS_MANY, 'Duty', 'updated_by'),
            'duties1' => array(self::HAS_MANY, 'Duty', 'responsible'),
            'dutyCategories' => array(self::HAS_MANY, 'DutyCategory', 'updated_by'),
            'dutyDatas' => array(self::HAS_MANY, 'DutyData', 'updated_by'),
            'dutySteps' => array(self::HAS_MANY, 'DutyStep', 'updated_by'),
            'materials' => array(self::HAS_MANY, 'Material', 'updated_by'),
            'materialGroups' => array(self::HAS_MANY, 'MaterialGroup', 'updated_by'),
            'materialGroupToMaterials' => array(self::HAS_MANY, 'MaterialGroupToMaterial', 'updated_by'),
            'materialToClients' => array(self::HAS_MANY, 'MaterialToClient', 'updated_by'),
            'plannings' => array(self::HAS_MANY, 'Planning', 'updated_by'),
            'plannings1' => array(self::HAS_MANY, 'Planning', 'in_charge_id'),
            'projects' => array(self::HAS_MANY, 'Project', 'updated_by'),
            'projectTemplates' => array(self::HAS_MANY, 'ProjectTemplate', 'updated_by'),
            'projectTemplateToAuthItems' => array(self::HAS_MANY, 'ProjectTemplateToAuthItem', 'updated_by'),
            'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'updated_by'),
            'projectToCustomFieldToProjectTemplates' => array(self::HAS_MANY, 'ProjectToCustomFieldToProjectTemplate', 'updated_by'),
            'projectToProjectTemplateToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTemplateToAuthItem', 'updated_by'),
            'purchaseOrders' => array(self::HAS_MANY, 'PurchaseOrder', 'updated_by'),
            'reports' => array(self::HAS_MANY, 'Report', 'updated_by'),
            'reportToAuthItems' => array(self::HAS_MANY, 'ReportToAuthItem', 'updated_by'),
            'resources' => array(self::HAS_MANY, 'Resource', 'updated_by'),
            'resourceCategories' => array(self::HAS_MANY, 'ResourceCategory', 'updated_by'),
            'resourceDatas' => array(self::HAS_MANY, 'ResourceData', 'updated_by'),
            'resourceToSuppliers' => array(self::HAS_MANY, 'ResourceToSupplier', 'updated_by'),
            'stages' => array(self::HAS_MANY, 'Stage', 'updated_by'),
            'standards' => array(self::HAS_MANY, 'Standard', 'updated_by'),
            'subAssemblies' => array(self::HAS_MANY, 'SubAssembly', 'updated_by'),
            'subReports' => array(self::HAS_MANY, 'SubReport', 'updated_by'),
            'suppliers' => array(self::HAS_MANY, 'Supplier', 'updated_by'),
            'supplierContacts' => array(self::HAS_MANY, 'SupplierContact', 'updated_by'),
            'tasks' => array(self::HAS_MANY, 'Task', 'updated_by'),
            'taskTemplates' => array(self::HAS_MANY, 'TaskTemplate', 'updated_by'),
            'taskTemplateToAssemblies' => array(self::HAS_MANY, 'TaskTemplateToAssembly', 'updated_by'),
            'taskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskTemplateToAssemblyGroup', 'updated_by'),
            'taskTemplateToDutyTypes' => array(self::HAS_MANY, 'TaskTemplateToDutyType', 'updated_by'),
            'taskTemplateToMaterials' => array(self::HAS_MANY, 'TaskTemplateToMaterial', 'updated_by'),
            'taskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskTemplateToMaterialGroup', 'updated_by'),
            'taskTemplateToResources' => array(self::HAS_MANY, 'TaskTemplateToResource', 'updated_by'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'updated_by'),
            'taskToAssemblyToAssemblyToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'updated_by'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'updated_by'),
            'taskToCustomFieldToTaskTemplates' => array(self::HAS_MANY, 'TaskToCustomFieldToTaskTemplate', 'updated_by'),
            'taskToMaterials' => array(self::HAS_MANY, 'TaskToMaterial', 'updated_by'),
            'taskToMaterialToAssemblyToMaterials' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterial', 'updated_by'),
            'taskToMaterialToAssemblyToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'updated_by'),
            'taskToMaterialToTaskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'updated_by'),
            'taskToPurchaseOrders' => array(self::HAS_MANY, 'TaskToPurchaseOrder', 'updated_by'),
            'taskToResources' => array(self::HAS_MANY, 'TaskToResource', 'updated_by'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'users' => array(self::HAS_MANY, 'User', 'updated_by'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'password' => 'Password',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',
			'contact.first_name AS first_name',
			'contact.last_name AS last_name',
			'contact.phone_mobile AS phone_mobile',
			'contact.email AS email',
		);
		
		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('contact.first_name',$this->first_name,true);
		$criteria->compare('contact.last_name',$this->last_name,true);
		$criteria->compare('contact.phone_mobile',$this->phone_mobile,true);
		$criteria->compare('contact.email',$this->email,true);

		// with
		$criteria->with=array(
			'contact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('id');
		$columns[] = $this->linkThisColumn('first_name');
		$columns[] = $this->linkThisColumn('last_name');
        $columns[] = array(
			'name'=>'phone_mobile',
			'value'=>'CHtml::link($data->phone_mobile, "tel:".$data->phone_mobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'email',
			'value'=>'$data->email',
			'type'=>'email',
		);
		
		return $columns;
	}

/*	public static function getDisplayAttr()
	{
		return array(
			'contact->first_name',
			'contact->flast_name',
			'contact->email',
		);
	}*/ 
 
	/**
	 * perform one-way encryption on the password before we store it in the database
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