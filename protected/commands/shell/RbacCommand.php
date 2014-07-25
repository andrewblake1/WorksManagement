<?php
class RbacCommand extends CConsoleCommand
{
   
    private $_authManager;
 
    public function getHelp()
	{
		return <<<EOD
USAGE
  rbac

DESCRIPTION
  This command generates an initial RBAC authorization hierarchy.

EOD;
	}
	
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		//ensure that an authManager is defined as this is mandatory for creating an auth heirarchy
		if(($this->_authManager=Yii::app()->authManager)===null)
		{
		    echo "Error: an authorization manager, named 'authManager' must be con-figured to use this command.\n";
			echo "If you already added 'authManager' component in application con-figuration,\n";
			echo "please quit and re-enter the yiic shell.\n";

			return;
		}  
		
		//provide the oportunity for the use to abort the request
		echo "Would you like to continue? [Yes|No] ";
	   
	    //check the input from the user and continue if they indicated yes to the above question
	    if(!strncasecmp(trim(fgets(STDIN)),'y',1)) 
		{
//		    //first we need to remove all operations, roles, child relationship and as-signments
//			$this->_authManager->clearAll();

			// NB: these must be run first or there will be an integrity constraing violation against no updated_by
			// NB: these are just an initial user and should be changed once app is installed
			Yii::app()->db->createCommand("
				INSERT IGNORE INTO `tbl_contact` (id, `first_name`, `last_name`, `phone_mobile`, `email`) VALUES (1, 'first', 'last', NULL, 'username');
			")->execute();

			Yii::app()->db->createCommand("
				INSERT IGNORE INTO `tbl_user` (contact_id, `password`) VALUES (1, MD5('password'));
			")->execute();

			// SYSTEM ADMIN
			$systemAdminRole=$this->_authManager->createRole('system admin', 'System Administrator');
			 
			Yii::app()->db->createCommand("
				INSERT IGNORE INTO `AuthAssignment` (`id`, `itemname`, `userid`, `bizrule`, `data`, `updated_by`) VALUES (NULL, 'system admin', '1', NULL, NULL, '1');
			")->execute();

			$task=$this->_authManager->createTask('Action', 'Action task');
			$systemAdminRole->addChild('Action');
			$this->_authManager->createOperation('ActionRead', 'Action read');
			$task->addChild('ActionRead');

			$task=$this->_authManager->createTask('ActionToLabourResource', 'ActionToLabourResource task');
			$systemAdminRole->addChild('ActionToLabourResource');
			$this->_authManager->createOperation('ActionToLabourResourceRead', 'ActionToLabourResource read');
			$task->addChild('ActionToLabourResourceRead');

			$task=$this->_authManager->createTask('ActionToLabourResourceBranch', 'ActionToLabourResourceBranch task');
			$systemAdminRole->addChild('ActionToLabourResourceBranch');
			$this->_authManager->createOperation('ActionToLabourResourceBranchRead', 'ActionToLabourResourceBranch read');
			$task->addChild('ActionToLabourResourceBranchRead');

			$task=$this->_authManager->createTask('ActionToPlant', 'ActionToPlant task');
			$systemAdminRole->addChild('ActionToPlant');
			$this->_authManager->createOperation('ActionToPlantRead', 'ActionToPlant read');
			$task->addChild('ActionToPlantRead');

			$task=$this->_authManager->createTask('ActionToPlantBranch', 'ActionToPlantBranch task');
			$systemAdminRole->addChild('ActionToPlantBranch');
			$this->_authManager->createOperation('ActionToPlantBranchRead', 'ActionToPlantBranch read');
			$task->addChild('ActionToPlantBranchRead');

			$task=$this->_authManager->createTask('ActionToPlantToPlantCapability', 'ActionToPlantToPlantCapability task');
			$systemAdminRole->addChild('ActionToPlantToPlantCapability');
			$this->_authManager->createOperation('ActionToPlantToPlantCapabilityRead', 'ActionToPlantToPlantCapability read');
			$task->addChild('ActionToPlantToPlantCapabilityRead');

			$task=$this->_authManager->createTask('Assembly', 'Assembly task');
			$systemAdminRole->addChild('Assembly');
			$this->_authManager->createOperation('AssemblyRead', 'Assembly read');
			$task->addChild('AssemblyRead');

			$task=$this->_authManager->createTask('AssemblyGroup', 'AssemblyGroup task');
			$systemAdminRole->addChild('AssemblyGroup');
			$this->_authManager->createOperation('AssemblyGroupRead', 'AssemblyGroup read');
			$task->addChild('AssemblyGroupRead');

			$task=$this->_authManager->createTask('AssemblyGroupToAssembly', 'AssemblyGroupToAssembly task');
			$systemAdminRole->addChild('AssemblyGroupToAssembly');
			$this->_authManager->createOperation('AssemblyGroupToAssemblyRead', 'AssemblyGroupToAssembly read');
			$task->addChild('AssemblyGroupToAssemblyRead');

			$task=$this->_authManager->createTask('AssemblyToAssemblyGroup', 'AssemblyToAssemblyGroup task');
			$systemAdminRole->addChild('AssemblyToAssemblyGroup');
			$this->_authManager->createOperation('AssemblyToAssemblyGroupRead', 'AssemblyToAssemblyGroup read');
			$task->addChild('AssemblyToAssemblyGroupRead');

			$task=$this->_authManager->createTask('AssemblyToMaterial', 'AssemblyToMaterial task');
			$systemAdminRole->addChild('AssemblyToMaterial');
			$this->_authManager->createOperation('AssemblyToMaterialRead', 'AssemblyToMaterial read');
			$task->addChild('AssemblyToMaterialRead');

			$task=$this->_authManager->createTask('AssemblyToMaterialGroup', 'AssemblyToMaterialGroup task');
			$systemAdminRole->addChild('AssemblyToMaterialGroup');
			$this->_authManager->createOperation('AssemblyToMaterialGroupRead', 'AssemblyToMaterialGroup read');
			$task->addChild('AssemblyToMaterialGroupRead');

			$task=$this->_authManager->createTask('ClientToAssembly', 'ClientToAssembly task');
			$systemAdminRole->addChild('ClientToAssembly');
			$this->_authManager->createOperation('ClientToAssemblyRead', 'ClientToAssembly read');
			$task->addChild('ClientToAssemblyRead');

			$task=$this->_authManager->createTask('AuthAssignment', 'AuthAssignment task');
			$systemAdminRole->addChild('AuthAssignment');
			$this->_authManager->createOperation('AuthAssignmentRead', 'AuthAssignment read');
			$task->addChild('AuthAssignmentRead');

			$task=$this->_authManager->createTask('AuthItem', 'AuthItem task');
			$systemAdminRole->addChild('AuthItem');
			$this->_authManager->createOperation('AuthItemRead', 'AuthItem read');
			$task->addChild('AuthItemRead');

			$task=$this->_authManager->createTask('AuthItemChild', 'AuthItemChild task');
			$systemAdminRole->addChild('AuthItemChild');
			$this->_authManager->createOperation('AuthItemChildRead', 'AuthItemChild read');
			$task->addChild('AuthItemChildRead');

			$task=$this->_authManager->createTask('Client', 'Client task');
			$systemAdminRole->addChild('Client');
			$this->_authManager->createOperation('ClientRead', 'Client read');
			$task->addChild('ClientRead');

			$task=$this->_authManager->createTask('ClientToMaterial', 'ClientToMaterial task');
			$systemAdminRole->addChild('ClientToMaterial');
			$this->_authManager->createOperation('ClientToMaterialRead', 'ClientToMaterial read');
			$task->addChild('ClientToMaterialRead');

			$task=$this->_authManager->createTask('Contact', 'Contact task');
			$systemAdminRole->addChild('Contact');
			$this->_authManager->createOperation('ContactRead', 'Contact read');
			$task->addChild('ContactRead');

			$task=$this->_authManager->createTask('CustomField', 'CustomField task');
			$systemAdminRole->addChild('CustomField');
			$this->_authManager->createOperation('CustomFieldRead', 'CustomField read');
			$task->addChild('CustomFieldRead');

			$task=$this->_authManager->createTask('CustomFieldDutyStepCategory', 'CustomFieldDutyStepCategory task');
			$systemAdminRole->addChild('CustomFieldDutyStepCategory');
			$this->_authManager->createOperation('CustomFieldDutyStepCategoryRead', 'CustomFieldDutyStepCategory read');
			$task->addChild('CustomFieldDutyStepCategoryRead');

			$task=$this->_authManager->createTask('CustomFieldProjectCategory', 'CustomFieldProjectCategory task');
			$systemAdminRole->addChild('CustomFieldProjectCategory');
			$this->_authManager->createOperation('CustomFieldProjectCategoryRead', 'CustomFieldProjectCategory read');
			$task->addChild('CustomFieldProjectCategoryRead');

			$task=$this->_authManager->createTask('CustomFieldTaskCategory', 'CustomFieldTaskCategory task');
			$systemAdminRole->addChild('CustomFieldTaskCategory');
			$this->_authManager->createOperation('CustomFieldTaskCategoryRead', 'CustomFieldTaskCategory read');
			$task->addChild('CustomFieldTaskCategoryRead');

			$task=$this->_authManager->createTask('DefaultValue', 'DefaultValue task');
			$systemAdminRole->addChild('DefaultValue');
			$this->_authManager->createOperation('DefaultValueRead', 'DefaultValue read');
			$task->addChild('DefaultValueRead');

			$task=$this->_authManager->createTask('Drawing', 'Drawing task');
			$systemAdminRole->addChild('Drawing');
			$this->_authManager->createOperation('DrawingRead', 'Drawing read');
			$task->addChild('DrawingRead');

			$task=$this->_authManager->createTask('DrawingToAssembly', 'DrawingToAssembly task');
			$systemAdminRole->addChild('DrawingToAssembly');
			$this->_authManager->createOperation('DrawingToAssemblyRead', 'DrawingToAssembly read');
			$task->addChild('DrawingToAssemblyRead');

			$task=$this->_authManager->createTask('DrawingToMaterial', 'DrawingToMaterial task');
			$systemAdminRole->addChild('DrawingToMaterial');
			$this->_authManager->createOperation('DrawingToMaterialRead', 'DrawingToMaterial read');
			$task->addChild('DrawingToMaterialRead');

			$task=$this->_authManager->createTask('DutyStep', 'DutyStep task');
			$systemAdminRole->addChild('DutyStep');
			$this->_authManager->createOperation('DutyStepRead', 'DutyStep read');
			$task->addChild('DutyStepRead');

			$task=$this->_authManager->createTask('DutyStepBranch', 'DutyStepBranch task');
			$systemAdminRole->addChild('DutyStepBranch');
			$this->_authManager->createOperation('DutyStepBranchRead', 'DutyStepBranch read');
			$task->addChild('DutyStepBranchRead');

			$task=$this->_authManager->createTask('DutyStepDependency', 'DutyStepDependency task');
			$systemAdminRole->addChild('DutyStepDependency');
			$this->_authManager->createOperation('DutyStepDependencyRead', 'DutyStepDependency read');
			$task->addChild('DutyStepDependencyRead');

			$task=$this->_authManager->createTask('DutyStepToCustomField', 'DutyStepToCustomField task');
			$systemAdminRole->addChild('DutyStepToCustomField');
			$this->_authManager->createOperation('DutyStepToCustomFieldRead', 'DutyStepToCustomField read');
			$task->addChild('DutyStepToCustomFieldRead');

			$task=$this->_authManager->createTask('DutyStepToMode', 'DutyStepToMode task');
			$systemAdminRole->addChild('DutyStepToMode');
			$this->_authManager->createOperation('DutyStepToModeRead', 'DutyStepToMode read');
			$task->addChild('DutyStepToModeRead');

			$task=$this->_authManager->createTask('LabourResource', 'LabourResource task');
			$systemAdminRole->addChild('LabourResource');
			$this->_authManager->createOperation('LabourResourceRead', 'LabourResource read');
			$task->addChild('LabourResourceRead');

			$task=$this->_authManager->createTask('LabourResourceToSupplier', 'LabourResourceToSupplier task');
			$systemAdminRole->addChild('LabourResourceToSupplier');
			$this->_authManager->createOperation('LabourResourceToSupplierRead', 'LabourResourceToSupplier read');
			$task->addChild('LabourResourceToSupplierRead');

			$task=$this->_authManager->createTask('MaterialGroup', 'MaterialGroup task');
			$systemAdminRole->addChild('MaterialGroup');
			$this->_authManager->createOperation('MaterialGroupRead', 'MaterialGroup read');
			$task->addChild('MaterialGroupRead');

			$task=$this->_authManager->createTask('MaterialGroupToMaterial', 'MaterialGroupToMaterial task');
			$systemAdminRole->addChild('MaterialGroupToMaterial');
			$this->_authManager->createOperation('MaterialGroupToMaterialRead', 'MaterialGroupToMaterial read');
			$task->addChild('MaterialGroupToMaterialRead');

			$task=$this->_authManager->createTask('Material', 'Material task');
			$systemAdminRole->addChild('Material');
			$this->_authManager->createOperation('MaterialRead', 'Material read');
			$task->addChild('MaterialRead');

			$task=$this->_authManager->createTask('Mode', 'Mode task');
			$systemAdminRole->addChild('Mode');
			$this->_authManager->createOperation('ModeRead', 'Mode read');
			$task->addChild('ModeRead');

			$task=$this->_authManager->createTask('Plan', 'Plan task');
			$systemAdminRole->addChild('Plan');
			$this->_authManager->createOperation('PlanRead', 'Plan read');
			$task->addChild('PlanRead');

			$task=$this->_authManager->createTask('Plant', 'Plant task');
			$systemAdminRole->addChild('Plant');
			$this->_authManager->createOperation('PlantRead', 'Plant read');
			$task->addChild('PlantRead');

			$task=$this->_authManager->createTask('PlantCapability', 'PlantCapability task');
			$systemAdminRole->addChild('PlantCapability');
			$this->_authManager->createOperation('PlantCapabilityRead', 'PlantCapability read');
			$task->addChild('PlantCapabilityRead');

			$task=$this->_authManager->createTask('PlantToSupplier', 'PlantToSupplier task');
			$systemAdminRole->addChild('PlantToSupplier');
			$this->_authManager->createOperation('PlantToSupplierRead', 'PlantToSupplier read');
			$task->addChild('PlantToSupplierRead');

			$task=$this->_authManager->createTask('PlantToSupplierToPlantCapability', 'PlantToSupplierToPlantCapability task');
			$systemAdminRole->addChild('PlantToSupplierToPlantCapability');
			$this->_authManager->createOperation('PlantToSupplierToPlantCapabilityRead', 'PlantToSupplierToPlantCapability read');
			$task->addChild('PlantToSupplierToPlantCapabilityRead');

			$task=$this->_authManager->createTask('ProjectTemplate', 'ProjectTemplate task');
			$systemAdminRole->addChild('ProjectTemplate');
			$this->_authManager->createOperation('ProjectTemplateRead', 'ProjectTemplate read');
			$task->addChild('ProjectTemplateRead');

			$task=$this->_authManager->createTask('ProjectTemplateToAuthItem', 'ProjectTemplateToAuthItem task');
			$systemAdminRole->addChild('ProjectTemplateToAuthItem');
			$this->_authManager->createOperation('ProjectTemplateToAuthItemRead', 'ProjectTemplateToAuthItem read');
			$task->addChild('ProjectTemplateToAuthItemRead');

			$task=$this->_authManager->createTask('ProjectTemplateToCustomField', 'ProjectTemplateToCustomField task');
			$systemAdminRole->addChild('ProjectTemplateToCustomField');
			$this->_authManager->createOperation('ProjectTemplateToCustomFieldRead', 'ProjectTemplateToCustomField read');
			$task->addChild('ProjectTemplateToCustomFieldRead');

			$task=$this->_authManager->createTask('ProjectType', 'ProjectType task');
			$systemAdminRole->addChild('ProjectType');
			$this->_authManager->createOperation('ProjectTypeRead', 'ProjectType read');
			$task->addChild('ProjectTypeRead');

			$task=$this->_authManager->createTask('Report', 'Report task');
			$systemAdminRole->addChild('Report');
			$this->_authManager->createOperation('ReportRead', 'Report read');
			$task->addChild('ReportRead');

			$task=$this->_authManager->createTask('SubReport', 'SubReport task');
			$systemAdminRole->addChild('SubReport');
			$this->_authManager->createOperation('SubReportRead', 'SubReport read');
			$task->addChild('SubReportRead');

			$task=$this->_authManager->createTask('ReportToAuthItem', 'ReportToAuthItem task');
			$systemAdminRole->addChild('ReportToAuthItem');
			$this->_authManager->createOperation('ReportToAuthItemRead', 'ReportToAuthItem read');
			$task->addChild('ReportToAuthItemRead');

			$task=$this->_authManager->createTask('Stage', 'Stage task');
			$systemAdminRole->addChild('Stage');
			$this->_authManager->createOperation('StageRead', 'Stage read');
			$task->addChild('StageRead');

			$task=$this->_authManager->createTask('Standard', 'Standard task');
			$systemAdminRole->addChild('Standard');
			$this->_authManager->createOperation('StandardRead', 'Standard read');
			$task->addChild('StandardRead');

			$task=$this->_authManager->createTask('SubAssembly', 'SubAssembly task');
			$systemAdminRole->addChild('SubAssembly');
			$this->_authManager->createOperation('SubAssemblyRead', 'SubAssembly read');
			$task->addChild('SubAssemblyRead');

			$task=$this->_authManager->createTask('Supplier', 'Supplier task');
			$systemAdminRole->addChild('Supplier');
			$this->_authManager->createOperation('SupplierRead', 'Supplier read');
			$task->addChild('SupplierRead');

			$task=$this->_authManager->createTask('SupplierContact', 'SupplierContact task');
			$systemAdminRole->addChild('SupplierContact');
			$this->_authManager->createOperation('SupplierContactRead', 'SupplierContact read');
			$task->addChild('SupplierContactRead');

			$task=$this->_authManager->createTask('TaskTemplate', 'TaskTemplate task');
			$systemAdminRole->addChild('TaskTemplate');
			$this->_authManager->createOperation('TaskTemplateRead', 'TaskTemplate read');
			$task->addChild('TaskTemplateRead');

			$task=$this->_authManager->createTask('TaskTemplateToAction', 'TaskTemplateToAction task');
			$systemAdminRole->addChild('TaskTemplateToAction');
			$this->_authManager->createOperation('TaskTemplateToActionRead', 'TaskTemplateToAction read');
			$task->addChild('TaskTemplateToActionRead');

			$task=$this->_authManager->createTask('TaskTemplateToActionToLabourResource', 'TaskTemplateToActionToLabourResource task');
			$systemAdminRole->addChild('TaskTemplateToActionToLabourResource');
			$this->_authManager->createOperation('TaskTemplateToActionToLabourResourceRead', 'TaskTemplateToActionToLabourResource read');
			$task->addChild('TaskTemplateToActionToLabourResourceRead');

			$task=$this->_authManager->createTask('TaskTemplateToActionToPlant', 'TaskTemplateToActionToPlant task');
			$systemAdminRole->addChild('TaskTemplateToActionToPlant');
			$this->_authManager->createOperation('TaskTemplateToActionToPlantRead', 'TaskTemplateToActionToPlant read');
			$task->addChild('TaskTemplateToActionToPlantRead');

			$task=$this->_authManager->createTask('TaskTemplateToAssembly', 'TaskTemplateToAssembly task');
			$systemAdminRole->addChild('TaskTemplateToAssembly');
			$this->_authManager->createOperation('TaskTemplateToAssemblyRead', 'TaskTemplateToAssembly read');
			$task->addChild('TaskTemplateToAssemblyRead');

			$task=$this->_authManager->createTask('TaskTemplateToAssemblyGroup', 'TaskTemplateToAssemblyGroup task');
			$systemAdminRole->addChild('TaskTemplateToAssemblyGroup');
			$this->_authManager->createOperation('TaskTemplateToAssemblyGroupRead', 'TaskTemplateToAssemblyGroup read');
			$task->addChild('TaskTemplateToAssemblyGroupRead');

			$task=$this->_authManager->createTask('TaskTemplateToCustomField', 'TaskTemplateToCustomField task');
			$systemAdminRole->addChild('TaskTemplateToCustomField');
			$this->_authManager->createOperation('TaskTemplateToCustomFieldRead', 'TaskTemplateToCustomField read');
			$task->addChild('TaskTemplateToCustomFieldRead');

			$task=$this->_authManager->createTask('TaskTemplateToMutuallyExclusiveRole', 'TaskTemplateToMutuallyExclusiveRole task');
			$systemAdminRole->addChild('TaskTemplateToMutuallyExclusiveRole');
			$this->_authManager->createOperation('TaskTemplateToMutuallyExclusiveRoleRead', 'TaskTemplateToMutuallyExclusiveRole read');
			$task->addChild('TaskTemplateToMutuallyExclusiveRoleRead');

			$task=$this->_authManager->createTask('TaskTemplateToLabourResource', 'TaskTemplateToLabourResource task');
			$systemAdminRole->addChild('TaskTemplateToLabourResource');
			$this->_authManager->createOperation('TaskTemplateToLabourResourceRead', 'TaskTemplateToLabourResource read');
			$task->addChild('TaskTemplateToLabourResourceRead');

			$task=$this->_authManager->createTask('TaskTemplateToMaterial', 'TaskTemplateToMaterial task');
			$systemAdminRole->addChild('TaskTemplateToMaterial');
			$this->_authManager->createOperation('TaskTemplateToMaterialRead', 'TaskTemplateToMaterial read');
			$task->addChild('TaskTemplateToMaterialRead');

			$task=$this->_authManager->createTask('TaskTemplateToMaterialGroup', 'TaskTemplateToMaterialGroup task');
			$systemAdminRole->addChild('TaskTemplateToMaterialGroup');
			$this->_authManager->createOperation('TaskTemplateToMaterialGroupRead', 'TaskTemplateToMaterialGroup read');
			$task->addChild('TaskTemplateToMaterialGroupRead');

			$task=$this->_authManager->createTask('TaskTemplateToPlant', 'TaskTemplateToPlant task');
			$systemAdminRole->addChild('TaskTemplateToPlant');
			$this->_authManager->createOperation('TaskTemplateToPlantRead', 'TaskTemplateToPlant read');
			$task->addChild('TaskTemplateToPlantRead');

			$task=$this->_authManager->createTask('TaskTemplateToPlantCapability', 'TaskTemplateToPlantCapability task');
			$systemAdminRole->addChild('TaskTemplateToPlantCapability');
			$this->_authManager->createOperation('TaskTemplateToPlantCapabilityRead', 'TaskTemplateToPlantCapability read');
			$task->addChild('TaskTemplateToPlantCapabilityRead');

			$task=$this->_authManager->createTask('User', 'User task');
			$systemAdminRole->addChild('User');
			$this->_authManager->createOperation('UserRead', 'User read');
			$task->addChild('UserRead');

			$task=$this->_authManager->createTask('ClientContact', 'ClientContact task');
			$systemAdminRole->addChild('ClientContact');
			$this->_authManager->createOperation('ClientContactRead', 'ClientContact read');
			$task->addChild('ClientContactRead');

			$task=$this->_authManager->createTask('Crew', 'Crew task');
			$systemAdminRole->addChild('Crew');
			$this->_authManager->createOperation('CrewRead', 'Crew read');
			$task->addChild('CrewRead');

			$task=$this->_authManager->createTask('CustomValue', 'CustomValue task');
			$systemAdminRole->addChild('CustomValue');
			$this->_authManager->createOperation('CustomValueRead', 'CustomValue read');
			$task->addChild('CustomValueRead');

			$task=$this->_authManager->createTask('Day', 'Day task');
			$systemAdminRole->addChild('Day');
			$this->_authManager->createOperation('DayRead', 'Day read');
			$task->addChild('DayRead');

			$task=$this->_authManager->createTask('Duty', 'Duty task');
			$systemAdminRole->addChild('Duty');
			$this->_authManager->createOperation('DutyRead', 'Duty read');
			$task->addChild('DutyRead');

			$task=$this->_authManager->createTask('MutuallyExclusiveRole', 'MutuallyExclusiveRole task');
			$systemAdminRole->addChild('MutuallyExclusiveRole');
			$this->_authManager->createOperation('MutuallyExclusiveRoleRead', 'MutuallyExclusiveRole read');
			$task->addChild('MutuallyExclusiveRoleRead');

			$task=$this->_authManager->createTask('Planning', 'Planning task');
			$systemAdminRole->addChild('Planning');
			$this->_authManager->createOperation('PlanningRead', 'Planning read');
			$task->addChild('PlanningRead');

			$task=$this->_authManager->createTask('PlantDataToPlantCapability', 'PlantDataToPlantCapability task');
			$systemAdminRole->addChild('PlantDataToPlantCapability');
			$this->_authManager->createOperation('PlantDataToPlantCapabilityRead', 'PlantDataToPlantCapability read');
			$task->addChild('PlantDataToPlantCapabilityRead');

			$task=$this->_authManager->createTask('Project', 'Project task');
			$systemAdminRole->addChild('Project');
			$this->_authManager->createOperation('ProjectRead', 'Project read');
			$task->addChild('ProjectRead');

			$task=$this->_authManager->createTask('ProjectToClientContact', 'ProjectToClientContact task');
			$systemAdminRole->addChild('ProjectToClientContact');
			$this->_authManager->createOperation('ProjectToClientContactRead', 'ProjectToClientContact read');
			$task->addChild('ProjectToClientContactRead');

			$task=$this->_authManager->createTask('ProjectToAuthItem', 'ProjectToAuthItem task');
			$systemAdminRole->addChild('ProjectToAuthItem');
			$this->_authManager->createOperation('ProjectToAuthItemRead', 'ProjectToAuthItem read');
			$task->addChild('ProjectToAuthItemRead');

			$task=$this->_authManager->createTask('ProjectToAuthItemToAuthAssignment', 'ProjectToAuthItemToAuthAssignment task');
			$systemAdminRole->addChild('ProjectToAuthItemToAuthAssignment');
			$this->_authManager->createOperation('ProjectToAuthItemToAuthAssignmentRead', 'ProjectToAuthItemToAuthAssignment read');
			$task->addChild('ProjectToAuthItemToAuthAssignmentRead');

			$task=$this->_authManager->createTask('Task', 'Task task');
			$systemAdminRole->addChild('Task');
			$this->_authManager->createOperation('TaskRead', 'Task read');
			$task->addChild('TaskRead');

			$task=$this->_authManager->createTask('TaskToAction', 'TaskToAction task');
			$systemAdminRole->addChild('TaskToAction');
			$this->_authManager->createOperation('TaskToActionRead', 'TaskToAction read');
			$task->addChild('TaskToActionRead');

			$task=$this->_authManager->createTask('TaskToAssembly', 'TaskToAssembly task');
			$task2=$this->_authManager->createTask('TaskToAssemblyToAssemblyToAssemblyGroup', 'TaskToAssemblyToAssemblyToAssemblyGroup task');
			$task2=$this->_authManager->createTask('TaskToAssemblyToTaskTemplateToAssemblyGroup', 'TaskToAssemblyToTaskTemplateToAssemblyGroup task');
			$systemAdminRole->addChild('TaskToAssembly');
			$systemAdminRole->addChild('TaskToAssemblyToAssemblyToAssemblyGroup');
			$systemAdminRole->addChild('TaskToAssemblyToTaskTemplateToAssemblyGroup');
			$task->addChild('TaskToAssemblyToAssemblyToAssemblyGroup');
			$task->addChild('TaskToAssemblyToTaskTemplateToAssemblyGroup');
			$this->_authManager->createOperation('TaskToAssemblyRead', 'TaskToAssembly read');
			$task->addChild('TaskToAssemblyRead');
			$this->_authManager->createOperation('TaskToAssemblyToAssemblyToAssemblyGroupRead', 'TaskToAssemblyToAssemblyToAssemblyGroup read');
			$this->_authManager->createOperation('TaskToAssemblyToTaskTemplateToAssemblyGroupRead', 'TaskToAssemblyToTaskTemplateToAssemblyGroup read');
			$task->addChild('TaskToAssemblyToAssemblyToAssemblyGroupRead');
			$task->addChild('TaskToAssemblyToTaskTemplateToAssemblyGroupRead');
			$task2->addChild('TaskToAssemblyToAssemblyToAssemblyGroupRead');
			$task2->addChild('TaskToAssemblyToTaskTemplateToAssemblyGroupRead');

			$task=$this->_authManager->createTask('TaskToMaterial', 'TaskToMaterial task');
			$task2=$this->_authManager->createTask('TaskToMaterialToAssemblyToMaterialGroup', 'TaskToMaterialToAssemblyToMaterialGroup task');
			$task3=$this->_authManager->createTask('TaskToMaterialToTaskTemplateToMaterialGroup', 'TaskToMaterialToTaskTemplateToMaterialGroup task');
			$systemAdminRole->addChild('TaskToMaterial');
			$systemAdminRole->addChild('TaskToMaterialToAssemblyToMaterialGroup');
			$systemAdminRole->addChild('TaskToMaterialToTaskTemplateToMaterialGroup');
			$task->addChild('TaskToMaterialToAssemblyToMaterialGroup');
			$task->addChild('TaskToMaterialToTaskTemplateToMaterialGroup');
			$this->_authManager->createOperation('TaskToMaterialRead', 'TaskToMaterial read');
			$task->addChild('TaskToMaterialRead');
			$this->_authManager->createOperation('TaskToMaterialToAssemblyToMaterialGroupRead', 'TaskToMaterialToAssemblyToMaterialGroup read');
			$this->_authManager->createOperation('TaskToMaterialToTaskTemplateToMaterialGroupRead', 'TaskToMaterialToTaskTemplateToMaterialGroup read');
			$task->addChild('TaskToMaterialToAssemblyToMaterialGroupRead');
			$task->addChild('TaskToMaterialToTaskTemplateToMaterialGroupRead');
			$task2->addChild('TaskToMaterialToAssemblyToMaterialGroupRead');
			$task3->addChild('TaskToMaterialToTaskTemplateToMaterialGroupRead');

			$task=$this->_authManager->createTask('TaskToLabourResource', 'TaskToLabourResource task');
			$systemAdminRole->addChild('TaskToLabourResource');
			$this->_authManager->createOperation('TaskToLabourResourceRead', 'TaskToLabourResource read');
			$task->addChild('TaskToLabourResourceRead');

			$task=$this->_authManager->createTask('TaskToPlant', 'TaskToPlant task');
			$systemAdminRole->addChild('TaskToPlant');
			$this->_authManager->createOperation('TaskToPlantRead', 'TaskToPlant read');
			$task->addChild('TaskToPlantRead');

			// DEFAULT
			$defaultRole=$this->_authManager->createRole('default', 'Default', 'return !Yii::app()->user->isGuest;');
			$this->_authManager->createTask('DashboardDuty', 'DashboardDuty task');
			$defaultRole->addChild('DashboardDuty');
			$this->_authManager->createTask('DashboardTask', 'DashboardTask task');
			$defaultRole->addChild('DashboardTask');

			// PROJECT FIELD MANAGER
			// Creating a task which is parent of Project role that has the business rule we
			// want to check - params passed by project update, create, delete. This task is then to become child of this role hence
			// not interferring with existing project manager role but providing another path of acceptance or denial for field manager
			$task=$this->_authManager->createTask('ProjectFieldManger',
				'Project update and delete if is field manager assigned to project)',
				'return Project::checkContext($params["primaryKey"], "field manager");');
			$task->addChild('Project');

			//provide a message indicating success
			echo "Authorization hierarchy successfully generated.";
        } 
    }
}