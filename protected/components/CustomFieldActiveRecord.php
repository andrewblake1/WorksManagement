<?php

abstract class CustomFieldActiveRecord extends ActiveRecord
{
	protected $evalCustomFieldPivots;
	protected $evalClassEndToCustomFieldPivot;
	protected $evalColumnCustomFieldModelTemplateId;
	protected $evalColumnEndId;

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

	protected function createCustomFields(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;

// NB:: An error can occurr after updateing relations from gii as seems to drop this relation unless fk is duplicated in custom field model type
		// loop thru all custom fields pivots
		foreach(eval("return {$this->evalCustomFieldPivots};") as $customFieldPivot)
		{
			// create new modelToCustomFieldModelTemplate
			$endToCustomFieldPivot = eval("return {$this->evalClassEndToCustomFieldPivot};");
			$endToCustomFieldPivot->custom_field_to_task_template_id = $customFieldPivot->id;
			eval('$endToCustomFieldPivot->' . $this->evalColumnEndId . '= $this->id;');
			$endToCustomFieldPivot->custom_value_id = $customValue->id;
			$endToCustomFieldPivot->setDefault($customFieldPivot->customField);
			// attempt save
			$saved &= $endToCustomFieldPivot->dbCallback('save');
			// record any errors
			$models[] = $endToCustomFieldPivot;
		}
		
		return $saved;
	}

	public function updateCustomFields(&$models=array())
	{
		// initialise the saved variable to show no errors in case the are no
		// model customValues - otherwise will return null indicating a save error
		$saved = true;
		
		// loop thru all associated customFields
		foreach(eval("return {$this->evalEndToCustomFieldPivots};") as $endToCustomFieldPivot)
		{
			$endToCustomFieldPivot->custom_value=$_POST[get_class($endToCustomFieldPivot)][eval('return $endToCustomFieldPivot->' . $this->evalCustomFieldPivot . '->custom_field_id;')]['custom_value'];
			$saved &= $endToCustomFieldPivot->updateSave($models);
		}

		return $saved;
	}

}
?>