<?php

class DutyStepToCustomFieldController extends Controller
{
	public function __construct($id, $module = null) {
		
		ActionController::setTrail();
	
		parent::__construct($id, $module);
	}
	
	// override the tabs when viewing assemblies for a particular task
	public function setTabs($model) {
		parent::setTabs($model);
		
		$getParams = Controller::getValidGetParams('DutyStep');
		foreach(static::$tabs[0] as &$tab)
		{
			$tab['url'] = array_merge($tab['url'], $getParams);
		}
	}
}