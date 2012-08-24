<?php
/*
 * Autocomplete using a foreign key field
 */
class WMEJuiAutoCompleteTableColumn extends WMEJuiAutoCompleteField
{

    public function init()
    {
        $this->attribute = 'searchTableColumn';
        CHtml::resolveNameID($this->model, $this->attribute, $tempHtmlOpts);
        $id = $tempHtmlOpts['id'];
        $this->_fieldID = $id;
        $this->_saveID = $id . '_save';
        $this->_lookupID = $id .'_lookup';

		// create the ajax url including foreign key model name and existing get parameters
		$this->sourceUrl = Yii::app()->createUrl("DefaultValue/autocomplete");

		$this->_display[] = $this->model->table;
		$this->_display[] = $this->model->column;
		
		$this->_display=(!empty($value) ? implode(Yii::app()->params['delimiter']['display'], $this->_display) : '');

		parent::init(); // ensure necessary assets are loaded

		echo $this->form->labelEx($this->model, $this->attribute);
	}
	
    public function run()
    {
 
        parent::run();
		
		echo $this->form->error($this->model, $this->model->searchTableColumn);
    }
}

?>