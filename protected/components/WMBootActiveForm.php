<?php

/**
 * _form view widget
 * @param ActiveRecord $model the model
 */

Yii::import('bootstrap.widgets.BootActiveForm');

class WMBootActiveForm extends BootActiveForm
{
	private $controller;
	
	public $enableAjaxValidation=false;
	public $htmlOptions=array('class'=>'well');
	public $model;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->controller = $this->getController();
		$this->id="{$this->controller->modelName}-form";
		parent::init();
	}
 
    public function run()
    {
		$this->controller->widget('StaffFormWidget',array('model'=>$this->model,'form'=>$this));

		echo '<div class="form-actions">';
			$this->controller->widget('bootstrap.widgets.BootButton', array(
				'buttonType'=>'submit',
				'type'=>'primary',
				'label'=>$this->model->isNewRecord ? 'Create' : 'Save',
			));
		echo '</div>';
		
		parent::run();
	}

	public function dropDownListRow($model, $attribute, $data = array(), $htmlOptions = array())
	{
		return $this->inputRow(BootInput::TYPE_DROPDOWN, $model, $attribute, $data, $htmlOptions);
	}

}

?>
