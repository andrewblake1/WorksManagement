<?php

/**
 * Generic widgets
 */
class GenericWidget extends CWidget
{
	private $controller;
	public $form;
	public $generic;
	public $genericType;
	public $relationToGenericType; // working from a generic model to generic type

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
		$genericType = $this->genericType;
		$generic = $this->generic;
		// get array of column names in generic table
		$dataTypeColumnNames = GenericType::getDataTypeColumnNames();
		// get the attribute name to be saving to - post array hence []
		$attribute = "[{$this->generic->id}]".$dataTypeColumnNames[$genericType->data_type];
		// get the label
		$htmlOptions = array('labelOptions' => array('label'=>$genericType->description));
		
		// set Generic custom validators as per the associated generic type
		// NB: the array is just the relations names used in validationLookup for sql type to get at the genericType model from the generic model
		$generic->setCustomValidators($genericType, array('relationToGenericType'=>$this->relationToGenericType));
		
//TODO: should probably be sub-classing here and using inheritance instead of switch. Potentially GenericWidgers could be abstract base with a
// factory method or static factory method to create the sub types.
		// create the widget based on the generic validation type
		switch($genericType->validation_type)
		{
			case GenericType::validationTypeNone :
			case GenericType::validationTypePCRE :
				// if should be datepicker
				if($genericType->data_type == GenericType::dataTypeDate)
				{
					echo $this->form->datepickerRow($attribute, $htmlOptions, $generic);
				}
				// othewise text box widget
				else
				{
					echo $this->form->textFieldRow($attribute, $htmlOptions, $generic);
				}
				break;

			case GenericType::validationTypeRange :
				$range = explode('-', $genericType->validation_text);
				$this->form->rangeFieldRow($attribute, $range[0], $range[1], $htmlOptions, $generic);
				break;

			case GenericType::validationTypeSQLSelect :
				$sql = $genericType->validation_text;
				if(Yii::app()->params->listMax >= Yii::app()->db->createCommand("SELECT COUNT(*) FROM ($sql) alias1")->queryScalar())
				{
					// Drop down list widget
					echo $this->form->dropDownListRow($attribute, Yii::app()->db->createCommand($sql)->query(), $htmlOptions, $generic);
				}
				else
				{
					// EJuiAutocomplete
					$this->controller->widget('WMEJuiAutoCompleteGenericField', array(
						'model'=>$generic,
						'form'=>$this->form,
						'genericType'=>$genericType,
						'name'=>$attribute,
						'htmlOptions'=>$htmlOptions['labelOptions'],
					));
				}

				break;

			case GenericType::validationTypeValueList :
				// Drop down list widget
				// first need to get a list where array keys are the same as the display members
				$list = explode(',', $genericType->validation_text);
				
				echo $this->form->dropDownListRow($attribute, array_combine($list, $list), $htmlOptions, $generic);
				break;
		}
	}
	
}

?>