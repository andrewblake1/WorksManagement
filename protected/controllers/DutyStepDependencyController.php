<?php

class DutyStepDependencyController extends ActionController
{

	protected function createRedirect($model) {
		$params = array();
		
		if(isset($_POST['DutyStepDependency']['client_id']))
		{
			$params['client_id'] = $_POST['DutyStepDependency']['client_id'];
		}
		
		if(isset($_POST['DutyStepDependency']['project_template_id']))
		{
			$params['project_template_id'] = $_POST['DutyStepDependency']['project_template_id'];
		}
		
		parent::createRedirect($model, $params);
	}

}

?>