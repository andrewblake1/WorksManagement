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

			 // system admin
			 $systemAdminRole=$this->_authManager->createRole('system admin', 'System Administrator');
			 // create tasks
			 $task=$this->_authManager->createTask('Assembly', 'Assembly task');
			 $systemAdminRole->addChild('Assembly');
			 $this->_authManager->createOperation('AssemblyRead', 'Assembly read');
			 $task->addChild('AssemblyRead');
			 $task=$this->_authManager->createTask('AuthAssignment', 'AuthAssignment task');
			 $systemAdminRole->addChild('AuthAssignment');
			 $this->_authManager->createOperation('AuthAssignmentRead', 'AuthAssignment read');
			 $task->addChild('AuthAssignmentRead');
			 $task=$this->_authManager->createTask('AuthItem', 'AuthItem task');
			 $systemAdminRole->addChild('AuthItem');
			 $this->_authManager->createOperation('AuthItemRead', 'AuthItem read');
			 $task->addChild('AuthItemRead');
			 $task=$this->_authManager->createTask('Client', 'Client task');
			 $systemAdminRole->addChild('Client');
			 $this->_authManager->createOperation('ClientRead', 'Client read');
			 $task->addChild('ClientRead');
			 $task=$this->_authManager->createTask('ClientToTaskType', 'ClientToTaskType task');
			 $systemAdminRole->addChild('ClientToTaskType');
			 $this->_authManager->createOperation('ClientToTaskTypeRead', 'ClientToTaskType read');
			 $task->addChild('ClientToTaskTypeRead');
			 $task=$this->_authManager->createTask('ClientToTaskTypeToDutyType', 'ClientToTaskTypeToDutyType task');
			 $systemAdminRole->addChild('ClientToTaskTypeToDutyType');
			 $this->_authManager->createOperation('ClientToTaskTypeToDutyTypeRead', 'ClientToTaskTypeToDutyType read');
			 $task->addChild('ClientToTaskTypeToDutyTypeRead');
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
			 $task=$this->_authManager->createTask('TaskType', 'TaskType task');
			 $systemAdminRole->addChild('TaskType');
			 $this->_authManager->createOperation('TaskTypeRead', 'TaskType read');
			 $task->addChild('TaskTypeRead');

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
			 $task->addChild('AssemblyRead');
			 $task=$this->_authManager->createTask('ProjectToAuthAssignment', 'ProjectToAuthAssignment task');
			 $projectManagerRole->addChild('ProjectToAuthAssignment');
			 $this->_authManager->createOperation('ProjectToAuthAssignmentRead', 'ProjectToAuthAssignment read');
			 $task->addChild('ProjectToAuthAssignmentRead');
			 $task=$this->_authManager->createTask('ProjectToAuthAssignmentToClientToTaskTypeToDutyType', 'ProjectToAuthAssignmentToClientToTaskTypeToDutyType task');
			 $projectManagerRole->addChild('ProjectToAuthAssignmentToClientToTaskTypeToDutyType');
			 $this->_authManager->createOperation('ProjectToAuthAssignmentToClientToTaskTypeToDutyTypeRead', 'ProjectToAuthAssignmentToClientToTaskTypeToDutyType read');
			 $task->addChild('ProjectToAuthAssignmentToClientToTaskTypeToDutyTypeRead');
			 $task=$this->_authManager->createTask('ProjectToGenericProjectType', 'ProjectToGenericProjectType task');
			 $projectManagerRole->addChild('ProjectToGenericProjectType');
			 $this->_authManager->createOperation('ProjectToGenericProjectTypeRead', 'ProjectToGenericProjectType read');
			 $task->addChild('ProjectToGenericProjectTypeRead');
			 $task=$this->_authManager->createTask('PurchaseOrders', 'PurchaseOrders task');
			 $projectManagerRole->addChild('PurchaseOrders');
			 $this->_authManager->createOperation('PurchaseOrdersRead', 'PurchaseOrders read');
			 $task->addChild('PurchaseOrdersRead');
			 $task=$this->_authManager->createTask('Task', 'Task task');
			 $projectManagerRole->addChild('Task');
			 $this->_authManager->createOperation('TaskRead', 'Task read');
			 $task->addChild('TaskRead');
			 $task=$this->_authManager->createTask('TaskToAssembly', 'TaskToAssembly task');
			 $projectManagerRole->addChild('TaskToAssembly');
			 $this->_authManager->createOperation('TaskToAssemblyRead', 'TaskToAssembly read');
			 $task->addChild('TaskToAssemblyRead');
			 $task=$this->_authManager->createTask('TaskToGenericTaskType', 'TaskToGenericTaskType task');
			 $projectManagerRole->addChild('TaskToGenericTaskType');
			 $this->_authManager->createOperation('TaskToGenericTaskTypeRead', 'TaskToGenericTaskType read');
			 $task->addChild('TaskToGenericTaskTypeRead');
			 $task=$this->_authManager->createTask('TaskToResourceType', 'TaskToResourceType task');
			 $projectManagerRole->addChild('TaskToResourceType');
			 $this->_authManager->createOperation('TaskToResourceTypeRead', 'TaskToResourceType read');
			 $task->addChild('TaskToResourceTypeRead');

			 // Scheduler
			 $schedulerRole=$this->_authManager->createRole('scheduler', 'Scheduler');
			 // create tasks
			 $task=$this->_authManager->createTask('Reschedule', 'Reschedule task');
			 $schedulerRole->addChild('Reschedule');
			 $this->_authManager->createOperation('RescheduleRead', 'Reschedule read');
			 $task->addChild('RescheduleRead');

			 // default role
			 $defaultRole=$this->_authManager->createRole('default', 'Default',
					'return !Yii::app()->user->isGuest;');
			 // create task to allow update access if user is related to this task - this will use checkAccess in update action
			 $this->_authManager->createOperation('DutyUpdate', 'Duty update', 'return $data["userid"] == Yii::app()->user->id;');
			 // attach this to the Duty task so that higher users don't get denied when checking this in Duty update action
			 $dutyTask=$this->_authManager->getAuthItem('Duty');
			 $dutyTask->addChild('DutyUpdate');
			 // grant default users this operation
			 $defaultRole->addChild('DutyUpdate');
			 // grant default users read operation
			 $defaultRole->addChild('DutyRead');
			 // Grant read access to tasks and projects
			 $defaultRole->addChild('TaskRead');
			 $defaultRole->addChild('ProjectRead');
			 
			 // create hierachy amongst roles
			 $systemAdminRole->addChild('project manager');
			 $projectManagerRole->addChild('scheduler');
			 $schedulerRole->addChild('default');
			 
		     //provide a message indicating success
		     echo "Authorization hierarchy successfully generated.";
        } 
    }
}
