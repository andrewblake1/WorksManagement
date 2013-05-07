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

	// recursive to find our way thru relations to target fk model
	private function getRelation($model, $fKModelType, &$level)
	{
		// ensure recursion ends at 5 levels
		if($level < 5)
		{
			// get the associated relation - assuming only 1
			foreach($model->relations() as $relationName => $relation)
			{
				// if we have found the relation that uses this attribute which is a foreign key
				if($relation[0] == ActiveRecord::BELONGS_TO && $relation[2] == $this->attribute)
				{
					// if this takes us to our end model
					if($relation[1] == $fKModelType)
					{
						return $model->$relationName;
					}
					// otherwise we need to dig a level deeper
					elseif(!empty($model->$relationName))
					{
						return $this->getRelation($model->$relationName, $fKModelType, ++$level);
					}
				}
			}
		}
	}
		
    public function init()
    {
		$attr = $this->attribute;
		$fKModelType = $this->fKModelType;

		CHtml::resolveNameID($this->model, $attr, $tempHtmlOpts);
		$id = $tempHtmlOpts['id'];
		$this->_fieldID = $id;
		$this->_saveID = $id . '_save';
		$this->_lookupID = $id .'_lookup';

        $value = CHtml::resolveValue($this->model, $this->attribute);

		$model = $this->getRelation($this->model, $fKModelType, $level = 0);

		// find our way down to the end model
		foreach($fKModelType::getDisplayAttr() as $key => $field)
		{
			if(!empty($model))
			{
				if(is_numeric($key))
				{
					eval('$this->_display[] = $model->'."$field;");
				}
				else
				{
					eval('$this->_display[] = $model->'."$key->$field;");
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

		//NB: need to check _display not empty as in the case of foreign key field that allows nulls otherwise implode will crash
		$this->_display=((empty($value) && empty($this->_display)) ? '' : implode(Yii::app()->params['delimiter']['display'], $this->_display));

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