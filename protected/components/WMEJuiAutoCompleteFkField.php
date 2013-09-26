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

	// recursive to find our way thru relations to target fk model for given attribute
	private function getRelation($model, $fKModelType, &$level)
	{
		// first try specific controller function to get relation
		if($relation = $this->controller->getRelation($this->model, $this->attribute))
		{
			return $relation;
		}
		
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

		if($model = $this->getRelation($this->model, $fKModelType, $level = 0))
		{
			// find our way down to the end model
			foreach($fKModelType::getDisplayAttr() as $field)
			{
				eval('$this->_display[] = $model->' . str_replace('t.', '', $field) . ";");
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

		// allow for possible tooltips
		if($fKModelType::model()->toolTipAttribute)
		{
			Yii::app()->clientScript->registerScript("tooltip_$id", '
			$("ul").on("mouseover", function(e) {
				$("#' . $this->_lookupID. '").tooltip("destroy");
				var $e = $(e.target);
				if ($e.is("a#ui-active-menuitem.ui-corner-all")) {
					$("#' . $this->_lookupID. '").tooltip({
						trigger: "manual",
						placement: "top",
						title: $e.attr("data-original-title"),
					}).tooltip("show");
				} 
			});
			$("ul").on("mouseleave", function(e) {
				$("#' . $this->_lookupID. '").tooltip("destroy");
			});
			', CClientScript::POS_LOAD
			);
		}
		
		parent::init(); // ensure necessary assets are loaded
	}
	
    public function run()
    {
        parent::run();
		
		echo $this->form->error($this->model, $this->attribute);
    }
}

?>