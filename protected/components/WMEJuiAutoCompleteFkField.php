<?php
/*
 * Autocomplete using a foreign key field
 */
class WMEJuiAutoCompleteFkField extends WMEJuiAutoCompleteField
{

	/**
	 * @var string the foreign key field.
	 */
	public $fkField;
	/**
	 * @var string the relation name to the FK table
	 */
	public $relName;

    public function init()
    {
  		$relations = $this->model->relations();
		$fKModelType = $relations[$this->relName][1];
		$attr = $this->attribute = $relations[$this->relName][2];				//the FK field (from CJuiInputWidget)
        $tempHtmlOpts = array();
        CHtml::resolveNameID($this->model, $attr, $tempHtmlOpts);
        $id = $tempHtmlOpts['id'];
        $this->_fieldID = $id;
        $this->_saveID = $id . '_save';
        $this->_lookupID = $id .'_lookup';

        $value = CHtml::resolveValue($this->model, $this->attribute);

		foreach($fKModelType::getDisplayAttr() as $key => $field)
		{
			if(is_numeric($key))
			{
				eval('$this->_display[] = $this->model->{$this->relName}->'."$field;");
			}
			else
			{
				eval('$this->_display[] = $this->model->{$this->relName}->'."$key->$field;");
			}
		}
		
		// create the ajax url including foreign key model name and existing get parameters
		$this->sourceUrl = Yii::app()->createUrl("$fKModelType/autocomplete",
			array(
				'fk_model' => $fKModelType,
				'model' => get_class($this->model),
			) + $_GET); 

		$this->_display=(!empty($value) ? implode(Yii::app()->params['delimiter']['display'], $this->_display) : '');

		parent::init(); // ensure necessary assets are loaded

		echo $this->form->labelEx($this->model, $this->fkField);
	}
	
    public function run()
    {
 
        parent::run();
		
		echo $this->form->error($this->model, $this->fkField);
    }
}

?>