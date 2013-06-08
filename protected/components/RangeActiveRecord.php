<?php
// TODO: replace this with trait use once in php 5.4
abstract class RangeActiveRecord extends CActiveRecord
{
	public function setCustomValidatorsRange($rangeModel)
	{
$t = $rangeModel->attributes;
		if(empty($rangeModel->select))
		{
			$this->customValidators[] = array('quantity', 'numerical', 'min'=>$rangeModel->minimum, 'max'=>$rangeModel->maximum);
		}
		else
		{
			$this->customValidators[] = array('quantity', 'in', 'range'=>explode(',', $rangeModel->select));
		}

		// force a re-read of validators
		$this->getValidators(NULL, TRUE);
	}
	
	static function getDefaultValue($select, $minimum, $maximum)
	{
		// if single select value
		if(is_numeric($select))
		{
			$default = $select;
		}
		// otherwise if min and max are identical
		elseif($minimum === $maximum)
		{
			$default = $minimum;
		}
		else
		{
			$default = NULL;
		}
		
		return $default;
	}
	
	public function getDefault()
	{
		return static::getDefaultValue($this->select, $this->minimum, $this->maximum);
	}
}
?>
