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
			
			// NB: these must be run first or there will be an integrity constraing violation against no staff_id
			// NB: these are just an initial user and should be changed once app is installed
			Yii::app()->db->createCommand("
				INSERT INTO `staff` (`id`, `first_name`, `last_name`, `phone_mobile`, `email`, `password`, `deleted`, `staff_id`) VALUES (NULL, 'first', 'last', NULL, 'username', MD5('password'), '0', NULL);
			")->execute();
			
			 // system admin
			 $systemAdminRole=$this->_authManager->createRole('system admin', 'System Administrator');
			 
			Yii::app()->db->createCommand("
				INSERT INTO `AuthAssignment` (`id`, `itemname`, `userid`, `bizrule`, `data`, `deleted`, `staff_id`) VALUES (NULL, 'system admin', '1', NULL, NULL, '0', '1');
			")->execute();
			 
			 
			 // create tasks
			 $task=$this->_authManager->createTask('Assembly', 'Assembly task');
			 $systemAdminRole->addChild('Assembly');
			 $this->_authManager->createOperation('AssemblyRead', 'Assembly read');
			 $task->addChild('AssemblyRead');

			 $task=$this->_authManager->createTask('AssemblyToMaterial', 'AssemblyToMaterial task');
			 $systemAdminRole->addChild('AssemblyToMaterial');
			 $this->_authManager->createOperation('AssemblyToMaterialRead', 'AssemblyToMaterial read');
			 $task->addChild('AssemblyToMaterialRead');

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

			 $task=$this->_authManager->createTask('TaskType', 'TaskType task');
			 $systemAdminRole->addChild('TaskType');
			 $this->_authManager->createOperation('TaskTypeRead', 'TaskType read');
			 $task->addChild('TaskTypeRead');

			 $task=$this->_authManager->createTask('TaskTypeToAssembly', 'TaskTypeToAssembly task');
			 $systemAdminRole->addChild('TaskTypeToAssembly');
			 $this->_authManager->createOperation('TaskTypeToAssemblyRead', 'TaskTypeToAssembly read');
			 $task->addChild('TaskTypeToAssemblyRead');

			 $task=$this->_authManager->createTask('TaskTypeToMaterial', 'TaskTypeToMaterial task');
			 $systemAdminRole->addChild('TaskTypeToMaterial');
			 $this->_authManager->createOperation('TaskTypeToMaterialRead', 'TaskTypeToMaterial read');
			 $task->addChild('TaskTypeToMaterialRead');

			 $task=$this->_authManager->createTask('TaskTypeToDutyType', 'TaskTypeToDutyType task');
			 $systemAdminRole->addChild('TaskTypeToDutyType');
			 $this->_authManager->createOperation('TaskTypeToDutyTypeRead', 'TaskTypeToDutyType read');
			 $task->addChild('TaskTypeToDutyTypeRead');

			 $task=$this->_authManager->createTask('TaskTypeToResourceType', 'TaskTypeToResourceType task');
			 $systemAdminRole->addChild('TaskTypeToResourceType');
			 $this->_authManager->createOperation('TaskTypeToResourceTypeRead', 'TaskTypeToResourceType read');
			 $task->addChild('TaskTypeToResourceTypeRead');

			 $task=$this->_authManager->createTask('DefaultValue', 'DefaultValue task');
			 $systemAdminRole->addChild('DefaultValue');
			 $this->_authManager->createOperation('DefaultValueRead', 'DefaultValue read');
			 $task->addChild('DefaultValueRead');

			 $task=$this->_authManager->createTask('DutyType', 'DutyType task');
			 $systemAdminRole->addChild('DutyType');
			 $this->_authManager->createOperation('DutyTypeRead', 'DutyType read');
			 $task->addChild('DutyTypeRead');

			 $task=$this->_authManager->createTask('Dutycategory', 'Dutycategory task');
			 $systemAdminRole->addChild('Dutycategory');
			 $this->_authManager->createOperation('DutycategoryRead', 'Dutycategory read');
			 $task->addChild('DutycategoryRead');

			 $task=$this->_authManager->createTask('GenericProjectType', 'GenericProjectType task');
			 $systemAdminRole->addChild('GenericProjectType');
			 $this->_authManager->createOperation('GenericProjectTypeRead', 'GenericProjectType read');
			 $task->addChild('GenericProjectTypeRead');

			 $task=$this->_authManager->createTask('GenericTaskType', 'GenericTaskType task');
			 $systemAdminRole->addChild('GenericTaskType');
			 $this->_authManager->createOperation('GenericTaskTypeRead', 'GenericTaskType read');
			 $task->addChild('GenericTaskTypeRead');

			 $task=$this->_authManager->createTask('GenericType', 'GenericType task');
			 $systemAdminRole->addChild('GenericType');
			 $this->_authManager->createOperation('GenericTypeRead', 'GenericType read');
			 $task->addChild('GenericTypeRead');

			 $task=$this->_authManager->createTask('Genericprojectcategory', 'Genericprojectcategory task');
			 $systemAdminRole->addChild('Genericprojectcategory');
			 $this->_authManager->createOperation('GenericprojectcategoryRead', 'Genericprojectcategory read');
			 $task->addChild('GenericprojectcategoryRead');

			 $task=$this->_authManager->createTask('Generictaskcategory', 'Generictaskcategory task');
			 $systemAdminRole->addChild('Generictaskcategory');
			 $this->_authManager->createOperation('GenerictaskcategoryRead', 'Generictaskcategory read');
			 $task->addChild('GenerictaskcategoryRead');

			 $task=$this->_authManager->createTask('Material', 'Material task');
			 $systemAdminRole->addChild('Material');
			 $this->_authManager->createOperation('MaterialRead', 'Material read');
			 $task->addChild('MaterialRead');

			 $task=$this->_authManager->createTask('Plan', 'Plan task');
			 $systemAdminRole->addChild('Plan');
			 $this->_authManager->createOperation('PlanRead', 'Plan read');
			 $task->addChild('PlanRead');

			 $task=$this->_authManager->createTask('ProjectType', 'ProjectType task');
			 $systemAdminRole->addChild('ProjectType');
			 $this->_authManager->createOperation('ProjectTypeRead', 'ProjectType read');
			 $task->addChild('ProjectTypeRead');

			 $task=$this->_authManager->createTask('ProjectTypeToAuthItem', 'ProjectTypeToAuthItem task');
			 $systemAdminRole->addChild('ProjectTypeToAuthItem');
			 $this->_authManager->createOperation('ProjectTypeToAuthItemRead', 'ProjectTypeToAuthItem read');
			 $task->addChild('ProjectTypeToAuthItemRead');

			 $task=$this->_authManager->createTask('ProjectToGenericProjectType', 'ProjectToGenericProjectType task');
			 $systemAdminRole->addChild('ProjectToGenericProjectType');
			 $this->_authManager->createOperation('ProjectToGenericProjectTypeRead', 'ProjectToGenericProjectType read');
			 $task->addChild('ProjectToGenericProjectTypeRead');

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

			 $task=$this->_authManager->createTask('ResourceType', 'ResourceType task');
			 $systemAdminRole->addChild('ResourceType');
			 $this->_authManager->createOperation('ResourceTypeRead', 'ResourceType read');
			 $task->addChild('ResourceTypeRead');

			 $task=$this->_authManager->createTask('Resourcecategory', 'Resourcecategory task');
			 $systemAdminRole->addChild('Resourcecategory');
			 $this->_authManager->createOperation('ResourcecategoryRead', 'Resourcecategory read');
			 $task->addChild('ResourcecategoryRead');

			 $task=$this->_authManager->createTask('Staff', 'Staff task');
			 $systemAdminRole->addChild('Staff');
			 $this->_authManager->createOperation('StaffRead', 'Staff read');
			 $task->addChild('StaffRead');

			 $task=$this->_authManager->createTask('Supplier', 'Supplier task');
			 $systemAdminRole->addChild('Supplier');
			 $this->_authManager->createOperation('SupplierRead', 'Supplier read');
			 $task->addChild('SupplierRead');

			 $task=$this->_authManager->createTask('TaskToGenericTaskType', 'TaskToGenericTaskType task');
			 $systemAdminRole->addChild('TaskToGenericTaskType');
			 $this->_authManager->createOperation('TaskToGenericTaskTypeRead', 'TaskToGenericTaskType read');
			 $task->addChild('TaskToGenericTaskTypeRead');

			 // project manager
			 $projectManagerRole=$this->_authManager->createRole('project manager', 'Project manager');
			 // create tasks

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

			 $task=$this->_authManager->createTask('Generic', 'Generic task');
			 $projectManagerRole->addChild('Generic');
			 $this->_authManager->createOperation('GenericRead', 'Generic read');
			 $task->addChild('GenericRead');

			 $task=$this->_authManager->createTask('MaterialToTask', 'MaterialToTask task');
			 $projectManagerRole->addChild('MaterialToTask');
			 $this->_authManager->createOperation('MaterialToTaskRead', 'MaterialToTask read');
			 $task->addChild('MaterialToTaskRead');

			 $task=$this->_authManager->createTask('Project', 'Project task');
			 $projectManagerRole->addChild('Project');
			 $this->_authManager->createOperation('ProjectRead', 'Project read');
			 $task->addChild('ProjectRead');

			 $task=$this->_authManager->createTask('ProjectToProjectTypeToAuthItem', 'ProjectToProjectTypeToAuthItem task');
			 $projectManagerRole->addChild('ProjectToProjectTypeToAuthItem');
			 $this->_authManager->createOperation('ProjectToProjectTypeToAuthItemRead', 'ProjectToProjectTypeToAuthItem read');
			 $task->addChild('ProjectToProjectTypeToAuthItemRead');

			 $task=$this->_authManager->createTask('TaskTypeToDutyTypeToProjectTypeToAuthItem', 'TaskTypeToDutyTypeToProjectTypeToAuthItem task');
			 $projectManagerRole->addChild('TaskTypeToDutyTypeToProjectTypeToAuthItem');
			 $this->_authManager->createOperation('TaskTypeToDutyTypeToProjectTypeToAuthItemRead', 'TaskTypeToDutyTypeToProjectTypeToAuthItem read');
			 $task->addChild('TaskTypeToDutyTypeToProjectTypeToAuthItemRead');

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

			 $task=$this->_authManager->createTask('TaskToAssembly', 'TaskToAssembly task');
			 $projectManagerRole->addChild('TaskToAssembly');
			 $this->_authManager->createOperation('TaskToAssemblyRead', 'TaskToAssembly read');
			 $task->addChild('TaskToAssemblyRead');

			 $task=$this->_authManager->createTask('TaskToPurchaseOrder', 'TaskToPurchaseOrder task');
			 $projectManagerRole->addChild('TaskToPurchaseOrder');
			 $this->_authManager->createOperation('TaskToPurchaseOrderRead', 'TaskToPurchaseOrder read');
			 $task->addChild('TaskToPurchaseOrderRead');

			 $task=$this->_authManager->createTask('TaskToResourceType', 'TaskToResourceType task');
			 $projectManagerRole->addChild('TaskToResourceType');
			 $this->_authManager->createOperation('TaskToResourceTypeRead', 'TaskToResourceType read');
			 $task->addChild('TaskToResourceTypeRead');

			 // SCHEDULER
			 // NB: this role is hard coded into the task _form view
			 $schedulerRole=$this->_authManager->createRole('scheduler', 'Scheduler');
			 // create tasks
			 $schedulerRole->addChild('ProjectRead');
			 $schedulerRole->addChild('ClientRead');

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
			 
			 // Grant project manager project read
			 $projectManagerRole->addChild('ClientRead');
			 
			 // create hierachy amongst roles
			 $systemAdminRole->addChild('project manager');
			 $systemAdminRole->addChild('scheduler');

			 // FIELD MANAGER
			 $fieldManagerRole=$this->_authManager->createRole('field manager', 'Field manager');
			 // Creating a task which is parent of Project role that has the business rule we
			 // want to check - params passed by project update, create, delete. This task is then to become child of this role hence
			 // not interferring with existing project manager role but providing another path of acceptance or denial for field manager
			 $task=$this->_authManager->createTask(
					'ProjectFieldManger',
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
			 $storemanRole=$this->_authManager->createRole('storeman', 'Stores');
			 $storemanRole->addChild('TaskRead');
			 $storemanRole->addChild('CrewRead');
			 $storemanRole->addChild('DayRead');
			 $storemanRole->addChild('ProjectRead');
			 $storemanRole->addChild('ClientRead');
			 $storemanRole->addChild('Material');
			 $storemanRole->addChild('Assembly');
			 $storemanRole->addChild('PurchaseOrderRead');

		     //provide a message indicating success
		     echo "Authorization hierarchy successfully generated.";
        } 
    }
}
