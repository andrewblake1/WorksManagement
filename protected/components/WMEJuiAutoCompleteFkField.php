<?php
/*
 * Autocomplete using a foreign key field
 */
class WMEJuiAutoCompleteFkField extends WMEJuiAutoCompleteField
{
	/**
	 * @var array $scopes named scopes to pass thru in url to autocomplete.
	 */
	public $scopes = array();
	/**
	 * @var string $attribute the attribute.
	 */
	public $attribute;
	/**
	 * @var string $fKModelType the referenced model.
	 */
	public $fKModelType;
	/**
	 * @var string $relName the relation name.
	 */
//	public $relName;

    public function init()
    {
		$attr = $this->attribute;
		$fKModelType = $this->fKModelType;
//		$relName = $this->relName;

		CHtml::resolveNameID($this->model, $attr, $tempHtmlOpts);
		$id = $tempHtmlOpts['id'];
		$this->_fieldID = $id;
		$this->_saveID = $id . '_save';
		$this->_lookupID = $id .'_lookup';

        $value = CHtml::resolveValue($this->model, $this->attribute);

		// get the associated relation - assuming only 1
  		foreach($this->model->relations() as $relationName => $relation)
		{
			// if we have found the relation that uses this attribute which is a foreign key
			if($relation[2] == $this->attribute)
			{
				$relName = $relationName;
				break;
			}
		}
		
		
		foreach($fKModelType::getDisplayAttr() as $key => $field)
		{
			if(!empty($this->model->$relName))
			{
				if(is_numeric($key))
				{
					eval('$this->_display[] = $this->model->$relName->'."$field;");
				}
				else
				{
					eval('$this->_display[] = $this->model->$relName->'."$key->$field;");
				}
			}
		}
		
		// create the ajax url including foreign key model name and existing get parameters
		$this->sourceUrl = Yii::app()->createUrl("$fKModelType/autocomplete",
			array(
				'model' => get_class($this->model),
				'attribute' => $this->attribute,
			) + $_GET + ($this->scopes ? array('scopes'=>$this->scopes) : array())
		); 

		$this->_display=(!empty($value) ? implode(Yii::app()->params['delimiter']['display'], $this->_display) : '');


//		echo CHtml::label($fKModelType::getNiceName(), $id);
		echo $this->form->labelEx($this->model, $this->attribute);

		parent::init(); // ensure necessary assets are loaded

	}
	
    public function run()
    {
 
        parent::run();
		
		echo $this->form->error($this->model, $this->attribute);
    }
}

?>