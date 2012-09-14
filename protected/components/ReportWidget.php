<?php

/**
 * Admin view widget
 * @param ActiveRecord $model the model
 * @param array $columns the table columns to display in the grid view
 */
class ReportWidget extends CWidget
{
	private $_controller;
	public $report_html;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->_controller = $this->getController();

		parent::init();
	}
 
    public function run()
    {
		
		// display the report
		$this->_controller->renderText($this->report_html);

		parent::run();
	}
}

?>