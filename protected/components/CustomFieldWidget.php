<?php

class CustomFieldWidget extends CWidget
{
	private $controller;
	public $form;
	public $customValue;
	public $customField;
	public $relationToCustomField;

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

		// get the attribute name to be saving to - post array hence []
		$attribute = "[{$this->customValue->id}]custom_value";
		// get the label
		$htmlOptions = array('labelOptions' => array('label'=>$customField->description));
		// set up validation
		$customValue->customValidatorParams = array(
			'customField' => $customField,
			'params' => array('relationToCustomField'=>$this->relationToCustomField),
		);		
		
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
				$this->form->rangeFieldRow($attribute, NULL, $range[0], $range[1], NULL, NULL, $htmlOptions, $customValue);
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