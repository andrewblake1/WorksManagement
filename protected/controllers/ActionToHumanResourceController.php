<?php

class ActionToHumanResourceController extends Controller
{
	public function __construct($id, $module = null) {
		
		ActionController::setTrail();
	
		parent::__construct($id, $module);
	}
	
	protected function createRedirect($model)
	{
		parent::createRedirect($model, ActionController::getCreateRedirectParams($this->modelName));
	}
	
}
