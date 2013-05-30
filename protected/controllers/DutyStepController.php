<?php

class DutyStepController extends ActionController
{

	protected function createRedirect($model) {
		$params = array();
		
		if(isset($_GET['DutyStep']['client_id']))
		{
			$params['client_id'] = $_GET['DutyStep']['client_id'];
		}
		
		if(isset($_GET['DutyStep']['project_template_id']))
		{
			$params['project_template_id'] = $_GET['DutyStep']['project_template_id'];
		}
$t = $model->attributes;		
//		if(isset())
		parent::createRedirect($model, $params);
	}

}

?>