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
	public function setTabs($model) {
		$drawingController = new DrawingController(NULL);
		$_GET['parent_id'] = $_GET['drawing_id'];
		$drawingController->setTabs(NULL);
		static::$tabs = $drawingController->tabs;
		static::$tabs[sizeof(static::$tabs) - 1][1]['active'] = TRUE;
		$this->breadcrumbs = DrawingController::getBreadCrumbTrail();
	}
	
}