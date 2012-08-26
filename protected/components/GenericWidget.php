<?php

/**
 * Generic widgets
 */
class GenericWidget extends CWidget
{
	private $controller;
	public $form;
	public $relation_modelToGenericModelType;
	public $toGenericType;
	public $relation_genericModelType;

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
		// get generic type
		$genericType = $this->toGenericType->{$this->relation_genericModelType}->genericType;
		// get generic
		$generic = $this->toGenericType->generic;
		// get array of column names in generic table
		$dataTypeColumnNames = GenericType::getDataTypeColumnNames();
		// get the attribute name to be saving to - post array hence []
		$attribute = "[{$this->toGenericType->generic_id}]".$dataTypeColumnNames[$genericType->data_type];
		// get the label
		$htmlOptions = array('labelOptions' => array('label'=>$genericType->description));
		// set Generic custom validators as per the associated generic type
		$generic->setCustomValidators($genericType,
			array(
				'relation_modelToGenericModelType'=>$this->relation_modelToGenericModelType,
				'relation_genericModelType'=>$this->relation_genericModelType,
			)
		);
//TODO: should probably be sub-classing here and using inheritance instead of switch. Potentially GenericWidgers could be abstract base with a
// factory method or static factory method to create the sub types.
		// create the widget based on the generic validation type
		switch($genericType->validation_type)
		{
			case GenericType::validationTypeNone :
			case GenericType::validationTypePCRE :
				// Text box widget
				echo $this->form->textFieldRow($attribute, $htmlOptions, $generic);
				break;

			case GenericType::validationTypeRange :
				$range = explode('-', $genericType->validation_text);
				$min = $range[0];
				$max = $range[1];
				// Drop down list widget 
				echo $this->form->dropDownListRow($attribute, array_combine(range($min, $max), range($min, $max)), $htmlOptions, $generic);
				break;

			case GenericType::validationTypeSQLSelect :
				$sql = $genericType->validation_text;
				if(10 >= Yii::app()->db->createCommand("SELECT COUNT(*) FROM ($sql) alias1")->queryScalar())
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
				echo $this->form->dropDownListRow($attribute, explode(',', $genericType->validation_text), $htmlOptions, $generic);
				break;
		}
	}
	
}

?>