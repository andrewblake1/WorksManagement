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
		$t = static::$tabs;
		static::$tabs[sizeof(static::$tabs) - 1][1]['active'] = TRUE;

//		$tabs=array();
//		$this->addTab(Drawing::getNiceName($_GET['id']), Yii::app()->request->requestUri, $tabs, TRUE);
//		static::$tabs = array_merge(static::$tabs, array($tabs));

		// elimate irrelevant tabs
		$this->breadcrumbs = DrawingController::getBreadCrumbTrail();
	}
	
}