<?php

/**
 * Generic widgets
 */
class GenericWidgets extends TbActiveForm
{
	private $controller;
	public $model;
	public $form;
	public $relation_modelToGenericModelType;
	public $relation_modelToGenericModelTypes;
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
		// loop thru all the pivot table generic types associated to this model
		foreach($this->model->{$this->relation_modelToGenericModelTypes} as $toGenericType)
		{
			// get generic type
			$genericType = $toGenericType->{$this->relation_genericModelType}->genericType;
			// get generic
			$generic = $toGenericType->generic;
			// get array of column names in generic table
			$dataTypeColumnNames = GenericType::getDataTypeColumnNames();
			// get the attribute name to be saving to - post array hence []
			$attribute = "[$toGenericType->generic_id]".$dataTypeColumnNames[$genericType->data_type];
			// get the label
			$htmlOptions = array('labelOptions' => array('label'=>$genericType->description));
			// set Generic custom validators as per the associated generic type
			$generic->setCustomValidators($genericType,
				array(
					'relation_modelToGenericModelType'=>$this->relation_modelToGenericModelType,
					'relation_genericModelType'=>$this->relation_genericModelType,
				)
			);
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
	
}

?>
