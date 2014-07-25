<?php

/**
 * This is the model class for table "tbl_client_contact".
 *
 * The followings are the available columns in table 'tbl_client_contact':
 * @property integer $id
 * @property integer $client_id
 * @property integer $contact_id
 * @property string $role
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property Client $client
 * @property Contact $contact
 * @property ProjectToClientContact[] $projectToClientContacts
 */
class ClientContact extends ContactActiveRecord
{

	// search variables
	public $searchFirstName;
	public $searchLastName;
	public $searchEmail;
	public $searchPhoneMobile;
	public $searchPhoneHome;
	public $searchPhoneWork;
	public $searchPhoneFax;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'client_contact_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchFirstName', $this->searchFirstName, 'contact.first_name', true);
		$criteria->compareAs('searchLastName', $this->searchLastName, 'contact.last_name', true);
		$criteria->compareAs('searchEmail', $this->searchEmail, 'contact.email', true);
		$criteria->compareAs('searchPhoneMobile', $this->searchPhoneMobile, 'contact.phone_mobile', true);
		$criteria->compareAs('searchPhoneHome', $this->searchPhoneHome, 'contact.phone_home', true);
		$criteria->compareAs('searchPhoneWork', $this->searchPhoneWork, 'contact.phone_work', true);
		$criteria->compareAs('searchPhoneFax', $this->searchPhoneFax, 'contact.phone_fax', true);

		// with
		$criteria->with=array(
			'contact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]='first_name';
		$columns[]='last_name';
		$columns[]='role';
        $columns[] = array(
			'name'=>'phone_mobile',
			'value'=>'CHtml::link($data->phone_mobile, "tel:".$data->phone_mobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'phone_home',
			'value'=>'CHtml::link($data->phone_home, "tel:".$data->phone_home)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'phone_work',
			'value'=>'CHtml::link($data->phone_work, "tel:".$data->phone_work)',
			'type'=>'raw',
		);
		$columns[]='phone_fax';
        $columns[] = array(
			'name'=>'email',
			'value'=>'$data->email',
			'type'=>'email',
		);
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchFirstName',
			'searchLastName',
			'searchPhoneMobile',
			'searchEmail',
		);
	}

	public function scopeClient($client_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('client_id', $client_id);

		$this->getDbCriteria()->mergeWith($criteria);
	
		return $this;
	}
	
}

?>