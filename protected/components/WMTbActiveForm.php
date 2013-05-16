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
	public $showSubmit = true;	// true, false, hide - hide is there for use when file uploading as a hack as this button needs to be there for the
	// ajax validation and form submit to occur for some reason. Havn't investigated why yet. There will be a cleaner way to do this!
	public $submitOptions = array('class'=>'form-button btn btn-primary btn-large');
	public $enableAjaxValidation=true;
	public $htmlOptions;
	public $model;
	private $_action;
	public $models=null;
	private $_htmlOptionReadonly = array();
	// put focus to first non datepicker as if goes to datepicker then the datepicker will display
	// in admin view
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
		// add in tooltip functionality for input and select elements elements - using attrib data-original-title
		Yii::app()->clientScript->registerScript('tooltip','$("[data-original-title]").tooltip();',CClientScript::POS_READY);

		if(empty($this->htmlOptions))
		{
			$this->htmlOptions = array();
		}
		$this->htmlOptions += array('class'=>'well');
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
		elseif($this->showSubmit)
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
			$this->parent_fk = /*ActiveRecord*/$modelName::getParentForeignKey(Controller::getParentCrumb($modelName));
		}

		// display any validation errors
		echo $this->errorSummary($this->models ? $this->models : $this->model);
		
		parent::init();
	}
 
    public function run()
    {
		$this->hiddenField('updated_by');
		
		// pass thru the original controller so we know can potentially return here
		echo CHtml::hiddenField('controller', Yii::app()->controller->modelName);

		// if there is a parent foreing key i.e. if there is a level above this in our navigation structure
		if(!empty($this->parent_fk))
		{
			// add hidden field so gets carried into the model on submit
			$this->hiddenField($this->parent_fk);
		}
		
		// this complex because if compare bool/string 
		$showSubmit = !(is_string($this->showSubmit) && $this->showSubmit == 'hide');

		// button attributes
		if(Yii::app()->controller->action->id == 'admin' && ($this->_action == 'Create' || $this->_action == 'Update'))
		{
			if($showSubmit)
			{
				echo '<div class="modal-footer">';
			}
			echo CHtml::submitButton($this->_action, $this->submitOptions);
			if($showSubmit)
			{
				echo '</div>';
			}
		}
		elseif($this->_action == 'Update' || $this->_action == 'Create')	// no modal
		{
			if($showSubmit)
			{
				echo '<div class="form-actions">';
			}
			echo CHtml::submitButton($this->_action, $this->submitOptions);
			if($showSubmit)
			{
				echo '</div>';
			}
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

	public function textAreaRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		echo parent::textAreaRow($model ? $model : $this->model, $attribute,
				array('class'=>'span5') + $htmlOptions + $this->_htmlOptionReadonly);
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
		if(!empty($columns[$attribute]) && $columns[$attribute]->dbType == 'date')
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
		$model = $model ? $model : $this->model;

		$htmlOptions['options']['format'] = 'd M, yyyy';

		// if no write access
		if(!Controller::checkAccess(Controller::accessRead, get_class($model)))
		{
			// disable the datepicker so calendar doesn't pop up when user points
			$htmlOptions['options']['disabled'] = 'true';
		}
		$htmlOptions['id'] = $attribute;

		echo parent::datepickerRow($model, $attribute, $htmlOptions + $this->_htmlOptionReadonly);
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
	
	public function fileFieldRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		echo parent::fileFieldRow($model ? $model : $this->model, $attribute,
			array('class'=>'span5') + $htmlOptions + $this->_htmlOptionReadonly);
	}
	
	public function rangeFieldRow($attribute, $minimum, $maximum, $select = '', $quantity_tooltip = '', $htmlOptions = array(), $model = NULL) {
		
		$model = $model ? $model : $this->model;
		
		$htmlOptions['data-original-title'] = $quantity_tooltip;

		// if no quantity currently set
		if($model->$attribute === NULL)
		{
			// set local default - if single select then select, or if min === max then to min
			$model->$attribute = RangeActiveRecord::getDefaultValue($select, $minimum, $maximum);
		}
		
		if(empty($select))
		{
			// if nothing given
			if($minimum === NULL || $maximum === NULL)
			{
				$this->textFieldRow($attribute, $htmlOptions, $model);
			}
			// if single value
			elseif($minimum == $maximum)
			{
				$htmlOptions['options']['disabled'] = 'true';
				$this->textFieldRow($attribute, $htmlOptions, $model);
			}
			else
			{
				abs($minimum - $maximum) > Yii::app()->params->listMax
					? $this->textFieldRow($attribute, $htmlOptions, $model)
					: $this->dropDownListRow($attribute, array_combine(range($minimum, $maximum), range($minimum, $maximum)), $htmlOptions, $model);
			}
		}
		else
		{
			// first need to get a list where array keys are the same as the display members
			$list = explode(',', $select);
			$this->dropDownListRow($attribute, array_combine($list, $list), $htmlOptions, $model);
		}
	}
	
}

?>