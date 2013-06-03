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
		    //first we need to remove all operations, roles, child relationship and as-signments
			$this->_authManager->clearAll();

			// NB: these must be run first or there will be an integrity constraing violation against no updated_by
			// NB: these are just an initial user and should be changed once app is installed
			Yii::app()->db->createCommand("
				INSERT INTO `tbl_contact` (id, `first_name`, `last_name`, `phone_mobile`, `email`) VALUES (1, 'first', 'last', NULL, 'username');
			")->execute();

			Yii::app()->db->createCommand("
				INSERT INTO `tbl_user` (contact_id, `password`) VALUES (1, MD5('password'));
			")->execute();

			// system admin
			$systemAdminRole=$this->_authManager->createRole('system admin', 'System Administrator');
			 
			Yii::app()->db->createCommand("
				INSERT INTO `AuthAssignment` (`id`, `itemname`, `userid`, `bizrule`, `data`, `updated_by`) VALUES (NULL, 'system admin', '1', NULL, NULL, '1');
			")->execute();

			// create tasks
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

			$task=$this->_authManager->createTask('SubAssembly', 'SubAssembly task');
			$systemAdminRole->addChild('SubAssembly');
			$this->_authManager->createOperation('SubAssemblyRead', 'SubAssembly read');
			$task->addChild('SubAssemblyRead');

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

			$task=$this->_authManager->createTask('AssemblyToClient', 'AssemblyToClient task');
			$systemAdminRole->addChild('AssemblyToClient');
			$this->_authManager->createOperation('AssemblyToClientRead', 'AssemblyToClient read');
			$task->addChild('AssemblyToClientRead');

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

			$task=$this->_authManager->createTask('MaterialGroup', 'MaterialGroup task');
			$systemAdminRole->addChild('MaterialGroup');
			$this->_authManager->createOperation('MaterialGroupRead', 'MaterialGroup read');
			$task->addChild('MaterialGroupRead');

			$task=$this->_authManager->createTask('MaterialGroupToMaterial', 'MaterialGroupToMaterial task');
			$systemAdminRole->addChild('MaterialGroupToMaterial');
			$this->_authManager->createOperation('MaterialGroupToMaterialRead', 'MaterialGroupToMaterial read');
			$task->addChild('MaterialGroupToMaterialRead');

			$task=$this->_authManager->createTask('MaterialToClient', 'MaterialToClient task');
			$systemAdminRole->addChild('MaterialToClient');
			$this->_authManager->createOperation('MaterialToClientRead', 'MaterialToClient read');
			$task->addChild('MaterialToClientRead');

			$task=$this->_authManager->createTask('Drawing', 'Drawing task');
			$systemAdminRole->addChild('Drawing');
			$this->_authManager->createOperation('DrawingRead', 'Drawing read');
			$task->addChild('DrawingRead');

			$task=$this->_authManager->createTask('DrawingToAssembly', 'DrawingToAssembly task');
			$systemAdminRole->addChild('DrawingToAssembly');
			$this->_authManager->createOperation('DrawingToAssemblyRead', 'DrawingToAssembly read');
			$task->addChild('DrawingToAssemblyRead');

			$task=$this->_authManager->createTask('Stage', 'Stage task');
			$systemAdminRole->addChild('Stage');
			$this->_authManager->createOperation('StageRead', 'Stage read');
			$task->addChild('StageRead');

			$task=$this->_authManager->createTask('TaskTemplate', 'TaskTemplate task');
			$systemAdminRole->addChild('TaskTemplate');
			$this->_authManager->createOperation('TaskTemplateRead', 'TaskTemplate read');
			$task->addChild('TaskTemplateRead');

			$task=$this->_authManager->createTask('TaskTemplateToAssembly', 'TaskTemplateToAssembly task');
			$systemAdminRole->addChild('TaskTemplateToAssembly');
			$this->_authManager->createOperation('TaskTemplateToAssemblyRead', 'TaskTemplateToAssembly read');
			$task->addChild('TaskTemplateToAssemblyRead');

			$task=$this->_authManager->createTask('TaskTemplateToAssemblyGroup', 'TaskTemplateToAssemblyGroup task');
			$systemAdminRole->addChild('TaskTemplateToAssemblyGroup');
			$this->_authManager->createOperation('TaskTemplateToAssemblyGroupRead', 'TaskTemplateToAssemblyGroup read');
			$task->addChild('TaskTemplateToAssemblyGroupRead');

			$task=$this->_authManager->createTask('TaskTemplateToMaterial', 'TaskTemplateToMaterial task');
			$systemAdminRole->addChild('TaskTemplateToMaterial');
			$this->_authManager->createOperation('TaskTemplateToMaterialRead', 'TaskTemplateToMaterial read');
			$task->addChild('TaskTemplateToMaterialRead');

			$task=$this->_authManager->createTask('TaskTemplateToMaterialGroup', 'TaskTemplateToMaterialGroup task');
			$systemAdminRole->addChild('TaskTemplateToMaterialGroup');
			$this->_authManager->createOperation('TaskTemplateToMaterialGroupRead', 'TaskTemplateToMaterialGroup read');
			$task->addChild('TaskTemplateToMaterialGroupRead');

			$task=$this->_authManager->createTask('TaskTemplateToAction', 'TaskTemplateToAction task');
			$systemAdminRole->addChild('TaskTemplateToAction');
			$this->_authManager->createOperation('TaskTemplateToActionRead', 'TaskTemplateToAction read');
			$task->addChild('TaskTemplateToActionRead');

			$task=$this->_authManager->createTask('TaskTemplateToResource', 'TaskTemplateToResource task');
			$systemAdminRole->addChild('TaskTemplateToResource');
			$this->_authManager->createOperation('TaskTemplateToResourceRead', 'TaskTemplateToResource read');
			$task->addChild('TaskTemplateToResourceRead');

			$task=$this->_authManager->createTask('DefaultValue', 'DefaultValue task');
			$systemAdminRole->addChild('DefaultValue');
			$this->_authManager->createOperation('DefaultValueRead', 'DefaultValue read');
			$task->addChild('DefaultValueRead');

			$task=$this->_authManager->createTask('DutyStep', 'DutyStep task');
			$systemAdminRole->addChild('DutyStep');
			$this->_authManager->createOperation('DutyStepRead', 'DutyStep read');
			$task->addChild('DutyStepRead');

			$task=$this->_authManager->createTask('DutyStepDependency', 'DutyStepDependency task');
			$systemAdminRole->addChild('DutyStepDependency');
			$this->_authManager->createOperation('DutyStepDependencyRead', 'DutyStepDependency read');
			$task->addChild('DutyStepDependencyRead');

			$task=$this->_authManager->createTask('Action', 'Action task');
			$systemAdminRole->addChild('Action');
			$this->_authManager->createOperation('ActionRead', 'Action read');
			$task->addChild('ActionRead');

			$task=$this->_authManager->createTask('CustomFieldToProjectTemplate', 'CustomFieldToProjectTemplate task');
			$systemAdminRole->addChild('CustomFieldToProjectTemplate');
			$this->_authManager->createOperation('CustomFieldToProjectTemplateRead', 'CustomFieldToProjectTemplate read');
			$task->addChild('CustomFieldToProjectTemplateRead');

			$task=$this->_authManager->createTask('CustomFieldToTaskTemplate', 'CustomFieldToTaskTemplate task');
			$systemAdminRole->addChild('CustomFieldToTaskTemplate');
			$this->_authManager->createOperation('CustomFieldToTaskTemplateRead', 'CustomFieldToTaskTemplate read');
			$task->addChild('CustomFieldToTaskTemplateRead');

			$task=$this->_authManager->createTask('CustomField', 'CustomField task');
			$systemAdminRole->addChild('CustomField');
			$this->_authManager->createOperation('CustomFieldRead', 'CustomField read');
			$task->addChild('CustomFieldRead');

			$task=$this->_authManager->createTask('CustomFieldProjectCategory', 'CustomFieldProjectCategory task');
			$systemAdminRole->addChild('CustomFieldProjectCategory');
			$this->_authManager->createOperation('CustomFieldProjectCategoryRead', 'CustomFieldProjectCategory read');
			$task->addChild('CustomFieldProjectCategoryRead');

			$task=$this->_authManager->createTask('CustomFieldTaskCategory', 'CustomFieldTaskCategory task');
			$systemAdminRole->addChild('CustomFieldTaskCategory');
			$this->_authManager->createOperation('CustomFieldTaskCategoryRead', 'CustomFieldTaskCategory read');
			$task->addChild('CustomFieldTaskCategoryRead');

			$task=$this->_authManager->createTask('Material', 'Material task');
			$systemAdminRole->addChild('Material');
			$this->_authManager->createOperation('MaterialRead', 'Material read');
			$task->addChild('MaterialRead');

			$task=$this->_authManager->createTask('Plan', 'Plan task');
			$systemAdminRole->addChild('Plan');
			$this->_authManager->createOperation('PlanRead', 'Plan read');
			$task->addChild('PlanRead');

			$task=$this->_authManager->createTask('ProjectTemplate', 'ProjectTemplate task');
			$systemAdminRole->addChild('ProjectTemplate');
			$this->_authManager->createOperation('ProjectTemplateRead', 'ProjectTemplate read');
			$task->addChild('ProjectTemplateRead');

			$task=$this->_authManager->createTask('ProjectTemplateToAuthItem', 'ProjectTemplateToAuthItem task');
			$systemAdminRole->addChild('ProjectTemplateToAuthItem');
			$this->_authManager->createOperation('ProjectTemplateToAuthItemRead', 'ProjectTemplateToAuthItem read');
			$task->addChild('ProjectTemplateToAuthItemRead');

			$task=$this->_authManager->createTask('ProjectToCustomFieldToProjectTemplate', 'ProjectToCustomFieldToProjectTemplate task');
			$systemAdminRole->addChild('ProjectToCustomFieldToProjectTemplate');
			$this->_authManager->createOperation('ProjectToCustomFieldToProjectTemplateRead', 'ProjectToCustomFieldToProjectTemplate read');
			$task->addChild('ProjectToCustomFieldToProjectTemplateRead');

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

			$task=$this->_authManager->createTask('Resource', 'Resource task');
			$systemAdminRole->addChild('Resource');
			$this->_authManager->createOperation('ResourceRead', 'Resource read');
			$task->addChild('ResourceRead');

			$task=$this->_authManager->createTask('ResourceToSupplier', 'ResourceToSupplier task');
			$systemAdminRole->addChild('ResourceToSupplier');
			$this->_authManager->createOperation('ResourceToSupplierRead', 'ResourceToSupplier read');
			$task->addChild('ResourceToSupplierRead');

			$task=$this->_authManager->createTask('ResourceCategory', 'ResourceCategory task');
			$systemAdminRole->addChild('ResourceCategory');
			$this->_authManager->createOperation('ResourceCategoryRead', 'ResourceCategory read');
			$task->addChild('ResourceCategoryRead');

			$task=$this->_authManager->createTask('User', 'User task');
			$systemAdminRole->addChild('User');
			$this->_authManager->createOperation('UserRead', 'User read');
			$task->addChild('UserRead');

			$task=$this->_authManager->createTask('Standard', 'Standard task');
			$systemAdminRole->addChild('Standard');
			$this->_authManager->createOperation('StandardRead', 'Standard read');
			$task->addChild('StandardRead');

			$task=$this->_authManager->createTask('Supplier', 'Supplier task');
			$systemAdminRole->addChild('Supplier');
			$this->_authManager->createOperation('SupplierRead', 'Supplier read');
			$task->addChild('SupplierRead');

			$task=$this->_authManager->createTask('SupplierContact', 'SupplierContact task');
			$systemAdminRole->addChild('SupplierContact');
			$this->_authManager->createOperation('SupplierContactRead', 'SupplierContact read');
			$task->addChild('SupplierContactRead');

			// project manager
			$projectManagerRole=$this->_authManager->createRole('project manager', 'Project manager');
			// create tasks

			$task=$this->_authManager->createTask('ClientContact', 'ClientContact task');
			$projectManagerRole->addChild('ClientContact');
			$this->_authManager->createOperation('ClientContactRead', 'ClientContact read');
			$task->addChild('ClientContactRead');

			$task=$this->_authManager->createTask('Crew', 'Crew task');
			$projectManagerRole->addChild('Crew');
			$this->_authManager->createOperation('CrewRead', 'Crew read');
			$task->addChild('CrewRead');

			$task=$this->_authManager->createTask('Day', 'Day task');
			$projectManagerRole->addChild('Day');
			$this->_authManager->createOperation('DayRead', 'Day read');
			$task->addChild('DayRead');

			$task=$this->_authManager->createTask('Duty', 'Duty task');
			$projectManagerRole->addChild('Duty');
			$this->_authManager->createOperation('DutyRead', 'Duty read');
			$task->addChild('DutyRead');

			$task=$this->_authManager->createTask('CustomValue', 'CustomValue task');
			$projectManagerRole->addChild('CustomValue');
			$this->_authManager->createOperation('CustomValueRead', 'CustomValue read');
			$task->addChild('CustomValueRead');

			$task=$this->_authManager->createTask('TaskToMaterial', 'TaskToMaterial task');
			$task2=$this->_authManager->createTask('TaskToMaterialToAssemblyToMaterialGroup', 'TaskToMaterialToAssemblyToMaterialGroup task');
			$projectManagerRole->addChild('TaskToMaterial');
			$projectManagerRole->addChild('TaskToMaterialToAssemblyToMaterialGroup');
			$task->addChild('TaskToMaterialToAssemblyToMaterialGroup');
			$this->_authManager->createOperation('TaskToMaterialRead', 'TaskToMaterial read');
			$task->addChild('TaskToMaterialRead');
			$this->_authManager->createOperation('TaskToMaterialToAssemblyToMaterialGroupRead', 'TaskToMaterialToAssemblyToMaterialGroup read');
			$task->addChild('TaskToMaterialToAssemblyToMaterialGroupRead');
			$task2->addChild('TaskToMaterialToAssemblyToMaterialGroupRead');

			$task=$this->_authManager->createTask('Project', 'Project task');
			$projectManagerRole->addChild('Project');
			$this->_authManager->createOperation('ProjectRead', 'Project read');
			$task->addChild('ProjectRead');

			$task=$this->_authManager->createTask('ProjectToClientContact', 'ProjectToClientContact task');
			$projectManagerRole->addChild('ProjectToClientContact');
			$this->_authManager->createOperation('ProjectToClientContactRead', 'ProjectToClientContact read');
			$task->addChild('ProjectToClientContactRead');

			$task=$this->_authManager->createTask('ProjectToAuthItem', 'ProjectToAuthItem task');
			$projectManagerRole->addChild('ProjectToAuthItem');
			$this->_authManager->createOperation('ProjectToAuthItemRead', 'ProjectToAuthItem read');
			$task->addChild('ProjectToAuthItemRead');

			$task=$this->_authManager->createTask('ProjectToAuthItemToAuthAssignment', 'ProjectToAuthItemToAuthAssignment task');
			$projectManagerRole->addChild('ProjectToAuthItemToAuthAssignment');
			$this->_authManager->createOperation('ProjectToAuthItemToAuthAssignmentRead', 'ProjectToAuthItemToAuthAssignment read');
			$task->addChild('ProjectToAuthItemToAuthAssignmentRead');

			$task=$this->_authManager->createTask('TaskTemplateToActionToProjectTemplateToAuthItem', 'TaskTemplateToActionToProjectTemplateToAuthItem task');
			$projectManagerRole->addChild('TaskTemplateToActionToProjectTemplateToAuthItem');
			$this->_authManager->createOperation('TaskTemplateToActionToProjectTemplateToAuthItemRead', 'TaskTemplateToActionToProjectTemplateToAuthItem read');
			$task->addChild('TaskTemplateToActionToProjectTemplateToAuthItemRead');

			$task=$this->_authManager->createTask('PurchaseOrder', 'PurchaseOrder task');
			$projectManagerRole->addChild('PurchaseOrder');
			$this->_authManager->createOperation('PurchaseOrderRead', 'PurchaseOrder read');
			$task->addChild('PurchaseOrderRead');

			$task=$this->_authManager->createTask('Planning', 'Planning task');
			$projectManagerRole->addChild('Planning');
			$this->_authManager->createOperation('PlanningRead', 'Planning read');
			$task->addChild('PlanningRead');

			$task=$this->_authManager->createTask('Task', 'Task task');
			$projectManagerRole->addChild('Task');
			$this->_authManager->createOperation('TaskRead', 'Task read');
			$task->addChild('TaskRead');

			$task=$this->_authManager->createTask('TaskToAction', 'TaskToAction task');
			$projectManagerRole->addChild('TaskToAction');
			$this->_authManager->createOperation('TaskToActionRead', 'TaskToAction read');
			$task->addChild('TaskToActionRead');

			$task=$this->_authManager->createTask('TaskToAssembly', 'TaskToAssembly task');
			$task2=$this->_authManager->createTask('TaskToAssemblyToAssemblyToAssemblyGroup', 'TaskToAssemblyToAssemblyToAssemblyGroup task');
			$projectManagerRole->addChild('TaskToAssembly');
			$projectManagerRole->addChild('TaskToAssemblyToAssemblyToAssemblyGroup');
			$task->addChild('TaskToAssemblyToAssemblyToAssemblyGroup');
			$this->_authManager->createOperation('TaskToAssemblyRead', 'TaskToAssembly read');
			$task->addChild('TaskToAssemblyRead');
			$this->_authManager->createOperation('TaskToAssemblyToAssemblyToAssemblyGroupRead', 'TaskToAssemblyToAssemblyToAssemblyGroup read');
			$task->addChild('TaskToAssemblyToAssemblyToAssemblyGroupRead');
			$task2->addChild('TaskToAssemblyToAssemblyToAssemblyGroupRead');

			$task=$this->_authManager->createTask('TaskToCustomFieldToTaskTemplate', 'TaskToCustomFieldToTaskTemplate task');
			$systemAdminRole->addChild('TaskToCustomFieldToTaskTemplate');
			$this->_authManager->createOperation('TaskToCustomFieldToTaskTemplateRead', 'TaskToCustomFieldToTaskTemplate read');
			$task->addChild('TaskToCustomFieldToTaskTemplateRead');

			$task=$this->_authManager->createTask('TaskToPurchaseOrder', 'TaskToPurchaseOrder task');
			$projectManagerRole->addChild('TaskToPurchaseOrder');
			$this->_authManager->createOperation('TaskToPurchaseOrderRead', 'TaskToPurchaseOrder read');
			$task->addChild('TaskToPurchaseOrderRead');

			$task=$this->_authManager->createTask('TaskToResource', 'TaskToResource task');
			$projectManagerRole->addChild('TaskToResource');
			$this->_authManager->createOperation('TaskToResourceRead', 'TaskToResource read');
			$task->addChild('TaskToResourceRead');

			// SCHEDULER
			// NB: this role is hard coded into the task _form view
			$schedulerRole=$this->_authManager->createRole('scheduler', 'Scheduler');
			// create tasks
			$schedulerRole->addChild('ProjectRead');
			$schedulerRole->addChild('ClientRead');

			// SWITCHING OPERATOR
			$schedulerRole=$this->_authManager->createRole('switching operator', 'Switching Operator');
			// create tasks
			$schedulerRole->addChild('AssemblyToMaterialRead');
			$schedulerRole->addChild('AssemblyRead');
			$schedulerRole->addChild('AssemblyGroupRead');
			$schedulerRole->addChild('AssemblyToAssemblyGroupRead');
			$schedulerRole->addChild('AssemblyGroupToAssemblyRead');
			$schedulerRole->addChild('AssemblyToMaterialGroupRead');
			$schedulerRole->addChild('DrawingRead');
			$schedulerRole->addChild('DrawingToAssemblyRead');
			$schedulerRole->addChild('MaterialGroupRead');
			$schedulerRole->addChild('MaterialRead');
			$schedulerRole->addChild('MaterialGroupToMaterialRead');
			$schedulerRole->addChild('StandardRead');
			$schedulerRole->addChild('SubAssemblyRead');

			// DEFAULT
			$defaultRole=$this->_authManager->createRole('default', 'Default', 'return !Yii::app()->user->isGuest;');
			// create task to allow update access if user is related to this task - this will use checkAccess in update action
			$this->_authManager->createOperation('DutyUpdate', 'Duty update', 'return $params["assignedTo"] == Yii::app()->user->id;');
			// attach this to the Duty task so that higher users don't get denied when checking this in Duty update action
			$dutyTask=$this->_authManager->getAuthItem('Duty');
			$dutyTask->addChild('DutyUpdate');
			// grant default users this operation
			$defaultRole->addChild('DutyUpdate');
			// grant default users read operation
			$defaultRole->addChild('DutyRead');
			// Grant read access to tasks and projects
			$defaultRole->addChild('TaskRead');
			$defaultRole->addChild('CrewRead');
			$defaultRole->addChild('DayRead');
			$defaultRole->addChild('ProjectRead');
			$defaultRole->addChild('ClientRead');
			$defaultRole->addChild('Planning');
			// Grant default user access to dashboard
/*			$task=$this->_authManager->createTask('Dashboard', 'Dashboard task');
			$this->_authManager->createTask('DashboardDuty', 'DashboardDuty task');
			$task->addChild('DashboardDuty');
			$defaultRole->addChild('Dashboard');*/


			// Grant project manager project read
			$projectManagerRole->addChild('ClientRead');

			// create hierachy amongst roles
			$systemAdminRole->addChild('project manager');
			$systemAdminRole->addChild('scheduler');

			// FIELD MANAGER
			$fieldManagerRole=$this->_authManager->createRole('field manager', 'Field manager');
			$fieldManagerRole->addChild('switching operator');

			// PROJECT FIELD MANAGER
			// Creating a task which is parent of Project role that has the business rule we
			// want to check - params passed by project update, create, delete. This task is then to become child of this role hence
			// not interferring with existing project manager role but providing another path of acceptance or denial for field manager
			$task=$this->_authManager->createTask('ProjectFieldManger',
				'Project update and delete if is field manager assigned to project)',
				'return Project::checkContext($params["primaryKey"], "field manager");');
			$task->addChild('Project');
			$fieldManagerRole->addChild('ProjectFieldManger');

			// OFFICE ADMIN
			$offieAdministratorRole=$this->_authManager->createRole('office admin', 'Office administrator');
			$offieAdministratorRole->addChild('TaskRead');
			$offieAdministratorRole->addChild('CrewRead');
			$offieAdministratorRole->addChild('DayRead');
			$offieAdministratorRole->addChild('ProjectRead');
			$offieAdministratorRole->addChild('ClientRead');

			// OUTAGE PLANNER
			$outagePlannerRole=$this->_authManager->createRole('outage planner', 'Outage planner');
			$outagePlannerRole->addChild('TaskRead');
			$outagePlannerRole->addChild('CrewRead');
			$outagePlannerRole->addChild('DayRead');
			$outagePlannerRole->addChild('ProjectRead');
			$outagePlannerRole->addChild('ClientRead');
			$outagePlannerRole->addChild('Planning');

			// FOREMAN
			$foremanRole=$this->_authManager->createRole('foreman', 'Foreman');
			$foremanRole->addChild('TaskRead');
			$foremanRole->addChild('CrewRead');
			$foremanRole->addChild('DayRead');
			$foremanRole->addChild('ProjectRead');
			$foremanRole->addChild('ClientRead');
			$foremanRole->addChild('MaterialRead');
			$foremanRole->addChild('AssemblyRead');
			$foremanRole->addChild('PurchaseOrderRead');

			// STOREMAN
			$storemanRole=$this->_authManager->createRole('storeman', 'Standards');
			$storemanRole->addChild('TaskRead');
			$storemanRole->addChild('CrewRead');
			$storemanRole->addChild('DayRead');
			$storemanRole->addChild('ProjectRead');
			$storemanRole->addChild('ClientRead');
			$storemanRole->addChild('Material');
			$storemanRole->addChild('Assembly');
			$storemanRole->addChild('PurchaseOrderRead');

			// DATAENTRY
			$dataentryRole=$this->_authManager->createRole('dataentry', 'Data entry');
			$dataentryRole->addChild('Standard');
			$dataentryRole->addChild('Assembly');
			$dataentryRole->addChild('SubAssembly');
			$dataentryRole->addChild('AssemblyToAssemblyGroup');
			$dataentryRole->addChild('AssemblyToMaterial');
			$dataentryRole->addChild('AssemblyToMaterialGroup');
			$dataentryRole->addChild('AssemblyGroup');
			$dataentryRole->addChild('AssemblyGroupToAssembly');
			$dataentryRole->addChild('Material');
			$dataentryRole->addChild('MaterialGroup');
			$dataentryRole->addChild('MaterialGroupToMaterial');
			$dataentryRole->addChild('Drawing');

			//provide a message indicating success
			echo "Authorization hierarchy successfully generated.";
        } 
    }
}
