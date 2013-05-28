<?php

class ActionController extends Controller
{

	public function __construct($id, $module = null) {
		// adjust the trail to remove the invalid items so the correct breadcrumbs are generated as action can appear at 3 levels
		self::$trail = self::getTrail();
		
		if(isset($_GET['project_template_id']))
		{
			unset(self::$trail['Action']);
			unset(self::$trail['Client']['Action']);
		}
		elseif(isset($_GET['client_id']))
		{
			unset(self::$trail['Action']);
		}
		
		parent::__construct($id, $module);
	}
	
	protected function createRedirect($model) {
		$params = array();
		
		if(isset($_POST['Action']['client_id']))
		{
			$params['client_id'] = $_POST['Action']['client_id'];
		}
		
		if(isset($_POST['Action']['project_template_id']))
		{
			$params['project_template_id'] = $_POST['Action']['project_template_id'];
		}
		
		parent::createRedirect($model, $params);
	}

}

?>