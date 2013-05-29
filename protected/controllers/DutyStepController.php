<?php

class DutyStepController extends ActionController
{

	protected function createRedirect($model) {
		$params = array();
		
		if(isset($_POST['DutyStep']['client_id']))
		{
			$params['client_id'] = $_POST['DutyStep']['client_id'];
		}
		
		if(isset($_POST['DutyStep']['project_template_id']))
		{
			$params['project_template_id'] = $_POST['DutyStep']['project_template_id'];
		}
		
		parent::createRedirect($model, $params);
	}

}

?>