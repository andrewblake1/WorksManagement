<?php

/**
 * _form view widget
 * @param ActiveRecord $model the model
 */

Yii::import('bootstrap.widgets.TbActiveForm');

class WMTbActiveForm extends TbActiveForm
{
	private $controller;
	
	public $enableAjaxValidation=true;
	public $htmlOptions=array('class'=>'well');
	public $model;
	public $models=null;

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
       // this method is called by CController::beginWidget()
		$this->controller = $this->getController();
		$this->id="{$this->controller->modelName}-form";
		$this->action = /*$this->controller->modelName .*/ ($this->model->isNewRecord ? 'create' : 'update');
        $this->clientOptions = array('validateOnSubmit'=>true);
		echo $this->errorSummary($this->models ? $this->models : $this->model);
		parent::init();
	}
 
    public function run()
    {
		StaffController::listWidgetRow($this->model, $this, 'staff_id');

		echo '<div class="form-actions">';
			$this->controller->widget('bootstrap.widgets.TbButton', array(
				'buttonType'=>'submit',
				'type'=>'primary',
				'label'=>$this->model->isNewRecord ? 'Create' : 'Save',
			));
		echo '</div>';
		
		parent::run();
	}

	/*
	 * automatically add max length attribute to inputs to save being in view file
	 * From http://www.yiiframework.com/forum/index.php/topic/3320-automatic-maxlength-attribute-for-input-fields-type-text-or-password/
	 */
	private static function maxLength($model, $attribute, $htmlOptions=array())
	{
		if(!isset($htmlOptions['maxlength']) && ($maxlength = $model->getAttributeMaxLength($attribute)))
		{
			$htmlOptions['maxlength'] = $maxlength;
		}
	}

	public function checkBoxRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		echo parent::checkBoxRow($model ? $model : $this->model, $attribute, $htmlOptions);
	}

	public function textAreaRow($attribute, $data = array(), $htmlOptions = array(), $model = NULL)
	{
		echo parent::textAreaRow($model ? $model : $this->model, $attribute, $data, array('rows'=>6, 'cols'=>50, 'class'=>'span8') + $htmlOptions);
	}
	
	public function dropDownListRow($attribute, $data = array(), $htmlOptions = array(), $model = NULL)
	{
		echo parent::dropDownListRow($model ? $model : $this->model, $attribute, $data, array('class'=>'span5') + $htmlOptions);
	}
	
	public function textFieldRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		self::maxLength($model ? $model : $this->model, $attribute, $htmlOptions);
		echo parent::textFieldRow($model ? $model : $this->model, $attribute, array('class'=>'span5') + $htmlOptions);
	}
	
	public function passwordFieldRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		self::maxLength($model ? $model : $this->model, $attribute, $htmlOptions);
		echo parent::passwordFieldRow($model ? $model : $this->model, $attribute, array('class'=>'span5') + $htmlOptions);
	}

}

?>
