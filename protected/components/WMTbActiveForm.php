<?php

/**
 * _form view widget
 * @param ActiveRecord $model the model
 */

Yii::import('bootstrap.widgets.TbActiveForm');

class WMTbActiveForm extends TbActiveForm
{
	private $controller;
	public $parent_fk;
	public $enableAjaxValidation=true;
	public $htmlOptions=array('class'=>'well');
	public $model;
	private $_action;
	public $models=null;
	private $_htmlOptionReadonly = array();
	// put focus to first non datepicker as if goes to datepicker then the datepicker will display
	// in admin view
//	public $focus = 'input:not([class="hasDatepicker"]):visible:enabled:first';
	public $focus = '[id^=myModal] input:not([class="hasDatepicker"]):visible:enabled:first, [id^=myModal] textarea:first';
    public $clientOptions = array(
		'validateOnSubmit'=>true,
		'validateOnChange'=>false,
		'afterValidate'=>'js: function(form, data, hasError)
		{
			// If adding/editing multiple models as a result of what appears visually to be a single model form
			// then their are errors returned in the json data object but hasError is false as it hasnt detected errors matching inputs
			// on the form as they dont exist. This function puts those erros into the error block at the top and stops the form being submitted
			var $lis = "";

			// if afterValidate is being told there are no errors what it really means is no form inputs have errors
			if(!hasError)
			{
			
				// loop thru json object which is 2 dimensional array
				$.each(data, function()
				{
					$.each(this, function(k, v)
					{
						$lis = $lis + "<li>" + v + "</li>";
					});
				});

				// if there are errors with the models but not on the form inputs
				if($lis != "")
				{
					$errorhtml = \'<div id="-form_es_" class="alert alert-block alert-error" style="">\
					<p>Please fix the following input errors:</p><ul>\' + $lis + \'</ul></div>\';
					
					$("[id*=-form_es_]").replaceWith($errorhtml);
					
					return false;
				}
			}

			return true;
		}'
);
//TODO: high priority - split this form into 4 - update and create modal, and update and create non modal - too many conditions
// likely this to become abstract with 4 polymorphic children
	/**
	 * Displays a particular model.
	 */
    public function init()
    {
		$this->controller = $this->getController();
		$modelName = get_class($this->model);
		$this->id="$modelName-form";
		
		// ensure the action if empty is an empty string and not null
		if(empty($this->action))
		{
			$this->action = '';
		}
		
		// determine whether form elements should be enabled or disabled by on access rights
		$controllerName = get_class($this->controller);
		if(!$controllerName::checkAccess(Controller::accessWrite))
		{
			$this->_htmlOptionReadonly = array('readonly'=>'readonly');
			$this->_action = 'View';
		}
		else
		{
			$this->_action = ($this->model->isNewRecord ? 'Create' : 'Update');
		}

		// Only do modal if in admin view
		if(/*$this->_action == 'Create' && */Yii::app()->controller->action->id == 'admin' || Yii::app()->controller->action->id == 'returnForm')
		{
			if($this->_action != 'Update')
			{
				$this->action = $this->controller->createUrl("$modelName/{$this->_action}");	// NB: this needed here but not for update to set the form action from admin as modal
			}
			echo '<div class="modal-header">';
			echo '<a class="close" data-dismiss="modal">&times;</a>';
			echo "<h3>{$modelName::getNiceName()}</h3>";
			echo '</div>';
		}

		// if no parent foreign key given
		if(empty($this->parent_fk))
		{
			// attmpt to get
			$this->parent_fk = /*ActiveRecord*/$modelName::getParentForeignKey($this->controller->getParentCrumb($modelName));
		}

		// display any validation errors
		echo $this->errorSummary($this->models ? $this->models : $this->model);
		
		parent::init();
	}
 
    public function run()
    {
/*		// only show the staff field when updating and if user is system admin in audit scenario
		if(!$this->model->isNewRecord && Yii::app()->user->checkAccess('system admin'))
		{
			StaffController::listWidgetRow($this->model, $this, 'staff_id', array('readonly'=>'readonly'));
		}
		else*/
		{
			$this->hiddenField('staff_id');
		}
		
		// pass thru the original controller so we know can potentially return here
		echo CHtml::hiddenField('controller', Yii::app()->controller->modelName);

		// if there is a parent foreing key i.e. if there is a level above this in our navigation structure
		if(!empty($this->parent_fk))
		{
$t = $this->model->attributes;
			// add hidden field so gets carried into the model on submit
			$this->hiddenField($this->parent_fk);
		}
		
		// button attributes
		$buttonOptions = array('class'=>'form-button btn btn-primary btn-large');
		if(Yii::app()->controller->action->id == 'admin' && ($this->_action == 'Create' || $this->_action == 'Update'))
		{
			echo '<div class="modal-footer">';
				echo CHtml::submitButton($this->_action, $buttonOptions);
			echo '</div>';
		}
		elseif($this->_action == 'Update' || $this->_action == 'Create')	// no modal
		{
			echo '<div class="form-actions">';
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

	public function checkBoxListInlineRow($attribute,  $data = array(), $htmlOptions = array(), $model = NULL)
	{
		echo parent::checkBoxListInlineRow($model ? $model : $this->model, 'preferred', $data, $htmlOptions);
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
		$model = $model ? $model : $this->model;
		
		// determine if date field
		$columns = $model->tableSchema->columns;
		if($columns[$attribute]->dbType == 'date')
		{
			 $this->datepickerRow($attribute, $htmlOptions ,$model);
		}
		else
		{
			self::maxLength($model, $attribute, $htmlOptions);
			echo parent::textFieldRow($model, $attribute,
				array('class'=>'span5') + $htmlOptions + $this->_htmlOptionReadonly);
		}
	}

	public function datepickerRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		// NB: the cool boostrap looking datepicker not working
// TODO: get working with the correct js and css which might be conflicting with something - isn't working anyway
		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery.ui');
		$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');

		$htmlOptions['options']['dateFormat'] = 'd M, yy';
		$htmlOptions['id'] = $attribute;

		echo parent::datepickerRow($model ? $model : $this->model, $attribute,
			array('class'=>'') + $htmlOptions + $this->_htmlOptionReadonly);
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