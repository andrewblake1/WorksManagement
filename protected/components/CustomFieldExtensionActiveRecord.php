<?php

abstract class CustomFieldExtensionActiveRecord extends ActiveRecord
{
	/*
	 * overidden as mulitple models
	 */
	public function createSave(&$models=array())
	{
		if($saved = $this->dbCallback('save'))
		{
			// attempt creation of customValues
			$saved &= $this->createCustomFields($models);
		}
		else
		{
			// put the model into the models array used for showing all errors
			$models[] = $this;
		}
		
		return $saved;
	}

	/*
	 * overidden as mulitple models
	 */
	public function updateSave(&$models=array())
	{
		if($saved = $this->dbCallback('save'))
		{
			// attempt creation of customValues
			$saved &= $this->updateCustomFields($models);
		}
		else
		{
			// put the model into the models array used for showing all errors
			$models[] = $this;
		}
		
		return $saved;
	}


}
?>