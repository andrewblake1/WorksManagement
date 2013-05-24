<?php

/**
 * CustomValue widgets
 */
class CustomFieldWidget extends CWidget
{
	private $controller;
	public $form;
	public $customValue;
	public $customField;
	public $CustomFieldModelType;
	public $relationToCustomField; // working from a customValue model to customValue type

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
		$customField = $this->customField;
		$customValue = $this->customValue;
		$CustomFieldModelType = $this->CustomFieldModelType;
		// get array of column names in customValue table
		$dataTypeColumnNames = CustomField::getDataTypeColumnNames();
		// get the attribute name to be saving to - post array hence []
		$attribute = "[$CustomFieldModelType->id]".$dataTypeColumnNames[$customField->data_type];
		// get the label
		$htmlOptions = array('labelOptions' => array('label'=>$customField->description));
		
		// set CustomValue custom validators as per the associated customValue type
		// NB: the array is just the relations names used in validationLookup for sql type to get at the customField model from the customValue model
		$customValue->setCustomValidators(array(
			'customField' => $customField,
			'params' => array('relationToCustomField'=>$this->relationToCustomField),
		));		
		
//TODO: should probably be sub-classing here and using inheritance instead of switch. Potentially CustomFieldWidgets could be abstract base with a
// factory method or static factory method to create the sub types.
		// create the widget based on the customValue validation type
		switch($customField->validation_type)
		{
			case CustomField::validation_typeNone :
			case CustomField::validation_typePCRE :
				// if should be datepicker
				if($customField->data_type == CustomField::data_typeDate)
				{
					echo $this->form->datepickerRow($attribute, $htmlOptions, $customValue);
				}
				// othewise text box widget
				else
				{
					echo $this->form->textFieldRow($attribute, $htmlOptions, $customValue);
				}
				break;

			case CustomField::validation_typeRange :
				$range = explode('-', $customField->validation_text);
				$this->form->rangeFieldRow($attribute, $range[0], $range[1], NULL, NULL, $htmlOptions, $customValue);
				break;

			case CustomField::validation_typeSQLSelect :
				$sql = $customField->validation_text;
				if(Yii::app()->params->listMax >= Yii::app()->db->createCommand("SELECT COUNT(*) FROM ($sql) alias1")->queryScalar())
				{
					// Drop down list widget
					echo $this->form->dropDownListRow($attribute, Yii::app()->db->createCommand($sql)->query(), $htmlOptions, $customValue);
				}
				else
				{
					// EJuiAutocomplete
					$this->controller->widget('WMEJuiAutoCompleteCustomField', array(
						'model'=>$customValue,
						'form'=>$this->form,
						'customField'=>$customField,
						'name'=>$attribute,
						'htmlOptions'=>$htmlOptions['labelOptions'],
					));
				}

				break;

			case CustomField::validation_typeValueList :
				// Drop down list widget
				// first need to get a list where array keys are the same as the display members
				$list = explode(',', $customField->validation_text);
				
				echo $this->form->dropDownListRow($attribute, array_combine($list, $list), $htmlOptions, $customValue);
				break;
		}
	}
	
}

?>