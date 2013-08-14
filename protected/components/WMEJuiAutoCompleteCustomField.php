<?php
/*
 * Autocomplete for customValue fields
 */
class WMEJuiAutoCompleteCustomField extends WMEJuiAutoCompleteField
{
	public $customField;
	public $ajax = false;

    public function init()
    {
		$attr = $this->attribute;

		CHtml::resolveNameID($this->model, $attr, $tempHtmlOpts);
		$id = $tempHtmlOpts['id'];
		$this->_fieldID = $id;
		$this->_saveID = $id . '_save';
		$this->_lookupID = $id .'_lookup';

        $value = CHtml::resolveValue($this->model, $this->attribute);
		
		$this->sourceUrl = Yii::app()->createUrl("customValue/autocomplete",
			array(
				'custom_field_id' => $this->customField->id,
		));

		$this->_display = $this->model->checkLookup($this->customField, $value, $this->attribute);

		if($this->customField->allow_new)
		{
			$this->htmlOptions['onkeyup'] = "$('#".$this->_fieldID."').val($(this).val());$('#".$this->_saveID."').val($(this).val());";
		}
		
		$this->htmlOptions['label'] = $this->htmlOptions['labelOptions']['label'];
		$this->htmlOptions['for'] = $this->_fieldID;
		unset($this->htmlOptions['labelOptions']);
		$this->htmlOptions['required']=  $this->customField->mandatory;
		echo $this->form->labelEx($this->model, $this->attribute, $this->htmlOptions);
		
		parent::init(); // ensure necessary assets are loaded
	}

    public function run()
    {
 
        parent::run();
		
		// if ajax then we need to ensure that we have the send back the script to bind also!
		if($this->ajax)
		{
			foreach(Yii::app()->getClientScript()->scripts as $value)
			{
				foreach($value as $key => $script)
				{
					if($key == "CJuiAutoComplete#{$this->_lookupID}")
					{
						echo CHtml::script($script);
					}
				}
			}
		}

		echo $this->form->error($this->model, $this->attribute, array('class'=>'help-block error'));
    }
}

?>