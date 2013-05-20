<?php
class ContactActiveRecord extends ActiveRecord
{
	protected $defaultSort = array('contact.email');
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Contact';
	
	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'first_name',
			'last_name',
			'email',
		);
	}

	public function afterFind() {
		$this->attributes = $this->contact->attributes;
		
		parent::afterFind();
	}

	public function insert($attributes = null)
	{
		$contact = new Contact();
		static::copyProperties($contact, $this, array('id'));
		$contact->insert();
		
		if($contact->errors)
		{
			$isErrors = TRUE;
			$this->addErrors($contact->errors);
		}
		else
		{
			$isErrors = FALSE;
			$this->contact_id = $contact->id;
			parent::insert($attributes);
		}
			
		return !$isErrors;
	}
		
	public function update($attributes = null)
	{
		$contact = Contact::model()->findByPk($this->contact_id);
		static::copyProperties($contact, $this, array('id'));
		$contact->update();
		
		if($contact->errors)
		{
			$isErrors = TRUE;
			$this->addErrors($contact->errors);
		}
		else
		{
			$isErrors = FALSE;
			parent::update($attributes);
		}
			
		return !$isErrors;
	}
	
// TODO: late at night - this easier than figuring out why massive assignment not working even
// with all variables safe in Contact. Fix to use massive assignment
	static function copyProperties(&$target, &$source, $ignore = array())
	{
		$targetSafeAttributeNames = $target->safeAttributeNames;
		foreach($source->safeAttributeNames as $attribute)
		{
			if(in_array($attribute, $targetSafeAttributeNames) && !in_array($attribute, $ignore))
			{
				$target->$attribute = $source->$attribute;
			}
		}
	}
}

?>