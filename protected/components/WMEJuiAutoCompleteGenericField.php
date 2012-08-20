<?php
/*
 * Autocomplete for generic fields
 */
class WMEJuiAutoCompleteGenericField extends WMEJuiAutoCompleteField
{
	/**
	 * @var string $genericType
	 */
	public $genericType;

    public function init()
    {
		$dataTypeColumnNames = GenericType::getDataTypeColumnNames();
		$dataTypeColumnName = $dataTypeColumnNames[$this->genericType->data_type];
		$this->sourceUrl = Yii::app()->createUrl("generic/autocomplete", array('generic_type_id' => $this->genericType->id));

        $tempHtmlOpts = array();
		$this->attribute = $this->name;
        CHtml::resolveNameID($this->model, $this->name, $tempHtmlOpts);
        $id = $tempHtmlOpts['id'];
        $this->_fieldID = $id;
        $this->_saveID = $id . '_save';
        $this->_lookupID = $id .'_lookup';
		$this->_display = $this->model->checkLookup($this->genericType, CHtml::resolveValue($this->model, $this->name), $this->name);
		// if allow_new
		if($this->genericType->allow_new)
		{
			$this->htmlOptions['onblur'] = "$('#".$this->_fieldID."').val($(this).val());";
		}
		
		parent::init(); // ensure necessary assets are loaded
		
		echo $this->form->labelEx($this->model, '', $this->htmlOptions);
	}
	
    public function run()
    {
 
        parent::run();
		
		echo $this->form->error($this->model, $this->attribute);
    }
}

?>