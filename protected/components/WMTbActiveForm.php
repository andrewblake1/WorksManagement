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
		if(!$this->model->checkAccess(Controller::accessWrite))
		{
			$this->_htmlOptionReadonly = array('readonly'=>'readonly');
			$this->_action = 'View';
		}
		elseif($this->showSubmit)
		{
			$this->_action = ($this->model->isNewRecord ? 'Create' : 'Update');
		}

		// Only do modal if in admin view
		$heading = $modelName::getCreateLabel();
		if(Yii::app()->controller->action->id == 'admin' || Yii::app()->controller->action->id == 'returnForm')
		{
			if(!$this->action && $this->_action != 'Update')
			{
				$this->action = $this->controller->createUrl("$modelName/{$this->_action}");	// NB: this needed here but not for update to set the form action from admin as modal
			}
			if($this->_action == 'View' || $this->_action == 'Update')
			{
				$heading = $modelName::getNiceNameShort(NULL, $this->model);
			}
			else
			{
				$heading = $modelName::getCreateLabel();
			}
			echo '<div class="modal-header">';
			echo '<a class="close" data-dismiss="modal">&times;</a>';
			echo "<h3>{$heading}</h3>";
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

		// use event bubbling to remove Please select blank value item after selections made on any select boxes within this form
		Yii::app()->clientScript->registerScript('dropDownListRow', "
			$('#{$this->id}').change(function(event){
				$('[value=\"\"]:not(:empty)', event.target).remove();
			});
			", CClientScript::POS_READY
		);
		
		parent::init();
	}
 
    public function run()
    {
//		$this->hiddenField('updated_by');
		
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

		// get button label
		$buttonLabel = $this->_action == 'Update'
			? $this->model->updateButtonText
			: $this->model->createButtonText;
		
		// button attributes
		if(Yii::app()->controller->action->id == 'admin' && ($this->_action == 'Create' || $this->_action == 'Update'))
		{
			if($showSubmit)
			{
				echo '<div class="modal-footer">';
			}
			echo CHtml::submitButton($buttonLabel, $this->submitOptions);
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
			echo CHtml::submitButton($buttonLabel, $this->submitOptions);
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
	private static function maxLength($model, $attribute, &$htmlOptions=array())
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
				$htmlOptions + array('class'=>'span5') + $this->_htmlOptionReadonly);
	}
	
	public function dropDownListRow($attribute, $data = array(), $htmlOptions = array(), $model = NULL)
	{
		$model = $model ? $model : $this->model;
		
		$required = $model->isAttributeRequired($attribute);

		// if no option to select and not required
		if(!sizeof($data) && !$required)
		{
			$this->hiddenField($attribute);
		}
		// if only 1 item in list and required
		elseif(sizeof($data) == 1 && $required)
		{
			// then no selection to be made so make it for the user - provided something has a value to exclude empty valued please selects
			if(current($data) !== NULL)
			{
				$id = CHtml::activeId($model, $attribute);
	
				// set val
				$model->$attribute = key($data);
				
				// create list box
				echo parent::dropDownListRow($model, $attribute,
					$data, $htmlOptions + array('class'=>'span5'));
				
				// add a dummy field as the list will be hidden on doc load
				echo CHtml::textField(NULL, current($data), array(
						'class'=>'span5',
						'disabled'=>'disabled',
						'id'=>$id . '_dummy',
					) + $htmlOptions);
				
				// trigger the change handler on document load
				Yii::app()->clientScript->registerScript("dropDownListRow_$id", "
					// trigger the change handler
					$('#$id').trigger('change');
					// hide the select
					$('#$id').hide();
					", CClientScript::POS_READY
				);
			}
		}
		// otherwise if multiple options or attribute required (need to show the empty list so admin can see why failing)
		else
		{
			echo parent::dropDownListRow($model, $attribute,
				$data,  $htmlOptions + array('class'=>'span5') + $this->_htmlOptionReadonly);
		}
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
		elseif(!empty($columns[$attribute]) && $columns[$attribute]->dbType == 'time')
		{
			 $this->timepickerRow($attribute, $htmlOptions ,$model);
		}
		else
		{
			self::maxLength($model, $attribute, $htmlOptions);
			echo parent::textFieldRow($model, $attribute,
				$htmlOptions + array('class'=>'span5') + $this->_htmlOptionReadonly);
		}
	}

	public function datepickerRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		$model = $model ? $model : $this->model;

		// if no write access
		if(!Controller::checkAccess(Controller::accessRead, get_class($model)))
		{
			// disable the datepicker so calendar doesn't pop up when user points
			$htmlOptions['disabled'] = 'true';
		}
		$htmlOptions['id'] = $attribute;
		$htmlOptions['options']['format'] = 'd M, yyyy';
		$htmlOptions['prepend'] = '<i class="icon-calendar"></i>';

		echo parent::datepickerRow($model, $attribute, $htmlOptions + $this->_htmlOptionReadonly);
	}

	public function timepickerRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		$model = $model ? $model : $this->model;

		// if no write access
		if(!Controller::checkAccess(Controller::accessRead, get_class($model)))
		{
			// disable the datepicker so calendar doesn't pop up when user points
			$htmlOptions['disabled'] = 'true';
		}
		$htmlOptions['id'] = $attribute;
		$htmlOptions['append'] = '<i class="icon-time"></i>';
		$htmlOptions['options']['showMeridian'] = false;
		$htmlOptions['options']['disableFocus'] = true;
		$htmlOptions['options']['defaultTime'] = false;

		echo parent::timepickerRow($model, $attribute, $htmlOptions + $this->_htmlOptionReadonly);
	}
	
	public function passwordFieldRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		self::maxLength($model ? $model : $this->model, $attribute, $htmlOptions);
		echo parent::passwordFieldRow($model ? $model : $this->model, $attribute,
			$htmlOptions + array('class'=>'span5') + $this->_htmlOptionReadonly);
	}

	public function hiddenField($attribute, $htmlOptions = array(), $model = NULL)
	{
		echo CHtml::activeHiddenField($model ? $model : $this->model, $attribute,
			$htmlOptions);
	}
	
	public function fileFieldRow($attribute, $htmlOptions = array(), $model = NULL)
	{
		echo parent::fileFieldRow($model ? $model : $this->model, $attribute,
			$htmlOptions + array('class'=>'span5') + $this->_htmlOptionReadonly);
	}
	
	public function rangeFieldRow($attribute, $default, $minimum, $maximum, $select = '', $quantity_tooltip = '', $htmlOptions = array(), $model = NULL) {
		
		$model = $model ? $model : $this->model;
		
		$htmlOptions['data-original-title'] = $quantity_tooltip;

		// if no quantity currently set
		if($model->$attribute === NULL)
		{
			// set local default - if single select then select, or if min === max then to min
			$model->$attribute = $default === NULL
				? $model::getDefaultValue($select, $minimum, $maximum)
				: $default;
		}
		// ensure value is in range
		elseif(($minimum !== null && $model->$attribute < $minimum) || ($maximum !== null && $model->$attribute > $maximum))
		{
			$model->$attribute = null;
		}
		
		if(empty($select))
		{
			// if nothing given
			if($minimum === NULL || $maximum === NULL)
			{
				$model->$attribute = $minimum;

				$this->textFieldRow($attribute, $htmlOptions, $model);
			}
			// if single value
			elseif($minimum == $maximum)
			{
				// ensure value is in range
				$model->$attribute = $minimum;

				$this->hiddenField($attribute, $htmlOptions, $model);
				// add a dummy field to display as the actual one will be hidden - disabled isn't su
				$htmlOptions['id'] = CHtml::activeId($model, $attribute) . '_dummy';
				$htmlOptions['disabled'] = 'disabled';
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
			if(sizeof($list) > 1)
			{
				$this->dropDownListRow($attribute, array_combine($list, $list), $htmlOptions, $model);
			}
			else
			{
				$this->hiddenField($attribute, $htmlOptions, $model);
				// add a dummy field to display as the actual one will be hidden - disabled isn't su
				$htmlOptions['id'] = CHtml::activeId($model, $attribute) . '_dummy';
				$htmlOptions['disabled'] = 'disabled';
				$this->textFieldRow($attribute, $htmlOptions, $model);
			}
		}
	}
	
}

?>