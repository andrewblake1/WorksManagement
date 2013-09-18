<?php
class ContactActiveRecord extends ActiveRecord
{
	public $first_name;
	public $last_name;
	public $email;
	public $address_line_1;
	public $address_line_2;
	public $post_code;
	public $town_city;
	public $state_province;
	public $country;
	public $phone_mobile;
	public $phone_home;
	public $phone_work;
	public $phone_fax;
	
	protected $defaultSort = array('email');
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Contact';
	
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->select=array(
			't.*',
			'contact.first_name AS first_name',
			'contact.last_name AS last_name',
			'contact.email AS email',
		);

		$criteria->with=array(
			'contact',
		);

		return $criteria;
	}

	public static function getDisplayAttr()
	{
		return array(
			'first_name',
			'last_name',
			'email',
		);
	}

	public function afterFind() {
		$this->copyProperties($this->contact);
		
		parent::afterFind();
	}

	public function insert($attributes = null)
	{
		$contact = new Contact();
		$contact->attributes = $_POST[get_class($this)];
		$contact->insert();
		
		if($contact->errors)
		{
			$isErrors = TRUE;
			$this->addErrors($contact->errors);
		}
		else
		{
			$this->contact_id = $contact->id;
			$isErrors = !parent::insert($attributes);
		}
			
		return !$isErrors;
	}
		
	public function update($attributes = null)
	{
		$contact = Contact::model()->findByPk($this->contact_id);
		$contact->attributes = $_POST[get_class($this)];
		$contact->update();
		
		if($contact->errors)
		{
			$isErrors = TRUE;
			$this->addErrors($contact->errors);
		}
		else
		{
			$isErrors = !parent::update($attributes);
		}
			
		return !$isErrors;
	}
	
	private function copyProperties(&$source, $ignore = array())
	{
		foreach($source->safeAttributeNames as $attribute)
		{
			$this->$attribute = $source->$attribute;
		}
	}
}

?>