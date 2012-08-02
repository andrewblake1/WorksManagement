<?php

/**
 * Staff widget for _form views.
 * @param ActiveRecord $model the model
 * @param Controller $controller the model
 * @param Form $form the surrounding form widget
 */
class StaffFormWidget extends CWidget
{
	private $controller;
	public $model;
	public $form;
	
	/**
	 * Displays a particular model.
	 */
    public function init()
    {
        // this method is called by CController::beginWidget()
		$this->controller = $this->getController();
	}
 
    public function run()
    {
        // this method is called by CController::endWidget()
		StaffController::listWidgetRow($this->model, $this->form, 'staff_id');
    }
}

?>
