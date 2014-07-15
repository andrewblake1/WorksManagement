<?php

class DrawingToAssemblyController extends Controller
{
	public function getButtons($model) {
		return array();
	}
	
	protected function newButton()
	{
		
	}
	
	// simulate the tabs in Drawing
	public function setTabs($model = NULL, &$tabs = NULL) {
		$drawingController = new DrawingController(NULL);
		$_GET['parent_id'] = $_GET['drawing_id'];
		$drawingController->setTabs(NULL);
		static::$tabs = $drawingController->tabs;
		$this->setActiveTabs(NULL, DrawingToAssembly::getNiceNamePlural());
		$this->breadcrumbs = DrawingController::getBreadCrumbTrail();
	}
	
}