<?php
trait RangeActiveRecordTrait
{
	public function setCustomValidatorsFromSource($rangeModel = NULL)
	{
		if($rangeModel)
		{
			if(empty($rangeModel->select))
			{
				$this->customValidators[] = array('quantity', 'numerical', 'min'=>$rangeModel->minimum, 'max'=>$rangeModel->maximum);
			}
			else
			{
				$this->customValidators[] = array('quantity', 'in', 'range'=>explode(',', $rangeModel->select));
			}
		}
// TODO: this re-engineer into activerecord when traits.
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
	
	public static function getDefault($rangeModel)
	{
		return static::getDefaultValue($rangeModel->select, $rangeModel->minimum, $rangeModel->maximum);
	}
}
?>
