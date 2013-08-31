<?php
class DbCriteria extends CDbCriteria
{
	public $select=array('t.*');

	// set up default comparison checks for t.*
	public function __construct(&$model = null, $ignores = array())
	{
		if($model === null)
		{
			return;
		}

		foreach($model->tableSchema->columns as $column)
		{
			$attribute = $column->name;
			
			// skip if empty
			if(empty($model->$attribute) || in_array($attribute, $ignores))
			{
				continue;
			}

			$partialMatch = true;

			switch($column->dbType)
			{
				case 'date' : 
				case 'time' : 
				case 'datetime' : 
					$partialMatch = false;
				default :
					if((strpos($column->dbType, 'int') !== FALSE) || $column->type == 'float')
					{
						$partialMatch = false;
					}
			}

			$this->compare("t.$attribute", $model->$attribute, $partialMatch);
		}
	}
	
	// turn compare into unordered search when term has spaces - i.e. like google etc
	public function compare($column, $value, $partialMatch=false, $operator='AND', $escape=true)
	{
		if($partialMatch && !is_array($value) && strpos($value, ' ') !== false && $operator=='AND')
		{
			foreach(explode(' ', $value) as $subString)
			{
				parent::compare($column, $subString, $partialMatch, $operator, $escape);
			}

			return $this;
		}
		else
			return parent::compare($column, $value, $partialMatch, $operator, $escape);
	}
	
	public function compareNull($column, $value = NULL)
	{
		if(empty($value))
		{
			$this->addCondition("$column IS NULL");
		}
		else
		{
			$this->compare($column, $value);
		}
	}

	public function compareAs($as, $term, $column, $partialMatch=false)
	{
		$this->compare($column, $term, $partialMatch);
		$this->select[] = "$column AS $as";
	}

	public function composite($as, $term, $columns)
	{
		$concat = "CONCAT_WS('" . Yii::app()->params['delimiter']['display'] . "', ". implode(', ', $columns) . ")";
		
		// if something has been entered
		if($term)
		{
			// protect against possible injection
			foreach($terms = explode(' ', $term) as $term)
			{
				$this->compare($concat, trim($term), true);
			}
		}
		
		// add to select
		$this->select[] = "$concat AS $as";
	}
}
?>