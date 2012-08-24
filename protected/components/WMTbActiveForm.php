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
	private $_action;
	public $models=null;
	private $_htmlOptionReadonly = array();
    public $clientOptions = array(
		'validateOnSubmit'=>true,
		'validateOnChange'=>false,
 	);

	/**
	 * Displays a particular model.
	 */
    public function init()
    {
		$this->controller = $this->getController();
		$modelName = $this->controller->modelName;
		$this->id="$modelName-form";
		
		// determine whether form elements should be enabled or disabled by on access rights
		if(!$this->controller->checkAccess(Controller::accessWrite))
		{
			$this->_htmlOptionReadonly = array('readonly'=>'readonly');
			$this->_action = 'View';
		}
		else
		{
			$this->_action = ($this->model->isNewRecord ? 'Create' : 'Update');
		}
		
		if($this->_action == 'Create')
		{
			$this->action = $this->_action;	// NB: this needed here but not for update to set the form action from admin as modal
			echo '<div class="modal-header">';
			echo '<a class="close" data-dismiss="modal">&times;</a>';
			echo "<h3>{$modelName::getNiceName()}</h3>";
			echo '</div>';
		}
		
		// display any validation errors
		echo $this->errorSummary($this->models ? $this->models : $this->model);
		
		parent::init();
	}
 
    public function run()
    {
		// only show the staff field when updating and if user is system admin
		if(!$this->model->isNewRecord && Yii::app()->user->checkAccess('system admin'))
		{
			StaffController::listWidgetRow($this->model, $this, 'staff_id', array('readonly'=>'readonly'));
		}
		else
		{
			$this->hiddenField('staff_id');
		}

		// button attributes
		$buttonOptions = array('class'=>'form-button btn btn-primary btn-large');
		// update
		if($this->_action == 'Update')
		{
			echo '<div class="form-actions">';
				echo CHtml::submitButton($this->_action, $buttonOptions);
			echo '</div>';
		}
		// create
		elseif($this->_action == 'Create')
		{
			echo '<div class="modal-footer">';
				echo CHtml::submitButton($this->_action, $buttonOptions);
			echo '</div>';
		}
		
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
		echo parent::checkBoxRow($model ? $model : $this->model, $attribute, $htmlOptions + $this->_htmlOptionReadonly);
	}

	public function textAreaRow($attribute, $data = array(), $htmlOptions = array(), $model = NULL)
	{
		echo parent::textAreaRow($model ? $model : $this->model, $attribute, $data,
				array('rows'=>6, 'cols'=>50, 'class'=>'span8') + $htmlOptions + $this->_htmlOptionReadonly);
	}
	
	public function dropDownListRow($attribute, $data = array(), $htmlOptions = array(), $model = NULL)
	{
		echo parent::dropDownListRow($model ? $model : $this->model, $attribute,
			$data, array('class'=>'span5') + $htmlOptions + $this->_htmlOptionReadonly);
	}
	
	public function textFieldRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		self::maxLength($model ? $model : $this->model, $attribute, $htmlOptions);
		echo parent::textFieldRow($model ? $model : $this->model, $attribute,
			array('class'=>'span5') + $htmlOptions + $this->_htmlOptionReadonly);
	}
	
	public function passwordFieldRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		self::maxLength($model ? $model : $this->model, $attribute, $htmlOptions);
		echo parent::passwordFieldRow($model ? $model : $this->model, $attribute,
			array('class'=>'span5') + $htmlOptions + $this->_htmlOptionReadonly);
	}

	public function hiddenField($attribute, $htmlOptions = array(), $model = NULL)
	{
		echo CHtml::activeHiddenField($model ? $model : $this->model, $attribute,
			$htmlOptions);
	}
	
}

?>