<?php
/*
 * Autocomplete for customValue fields
 */
class WMEJuiAutoCompleteCustomField extends WMEJuiAutoCompleteField
{
	/**
	 * @var string $customField
	 */
	public $customField;

    public function init()
    {
		$dataTypeColumnNames = CustomField::getDataTypeColumnNames();
		$data_typeColumnName = $dataTypeColumnNames[$this->customField->data_type];
		$this->sourceUrl = Yii::app()->createUrl("customValue/autocomplete", array('custom_field_id' => $this->customField->id));

        $tempHtmlOpts = array();
		$this->attribute = $this->name;
        CHtml::resolveNameID($this->model, $this->name, $tempHtmlOpts);
        $id = $tempHtmlOpts['id'];
        $this->_fieldID = $id;
        $this->_saveID = $id . '_save';
        $this->_lookupID = $id .'_lookup';
		$this->_display = $this->model->checkLookup($this->customField, CHtml::resolveValue($this->model, $this->name), $this->name);
		// if allow_new
		if($this->customField->allow_new)
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