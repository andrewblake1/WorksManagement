<?php
// TODO: replace this with trait use once in php 5.4
abstract class RangeActiveRecord extends CActiveRecord
{
	public function setCustomValidatorsRange($rangeModel)
	{
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
}
?>
