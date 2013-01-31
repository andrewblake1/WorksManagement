<?php
class DbCriteria extends CDbCriteria
{

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
	
/*	public function compareNull($column, $value)
	{
		if(empty($value))
		{
			$this->addCondition("$column IS NULL");
		}
		else
		{
			$this->compare($column, $value);
		}
	}*/

}
?>