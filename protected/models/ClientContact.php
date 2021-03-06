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
class ClientContact extends ActiveRecord
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
		$columns[]='role';
		$columns[] = 'searchFirstName';
		$columns[] = 'searchLastName';
        $columns[] = array(
			'name'=>'searchEmail',
			'value'=>'$data->searchEmail',
			'type'=>'email',
		);
		$columns[] = array(
			'name'=>'searchPhoneMobile',
			'value'=>'CHtml::link($data->searchPhoneMobile, "tel:".$data->searchPhoneMobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchPhoneHome',
			'value'=>'CHtml::link($data->searchPhoneHome, "tel:".$data->searchPhoneHome)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchPhoneWork',
			'value'=>'CHtml::link($data->searchPhoneWork, "tel:".$data->searchPhoneWork)',
			'type'=>'raw',
		);
		$columns[]='searchPhoneFax';
		
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