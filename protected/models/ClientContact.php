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


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array_merge(parent::rules(array('contact_id')), array(
			array('first_name, last_name, email', 'required'),
			array('first_name, last_name', 'length', 'max'=>64),
			array('email', 'length', 'max'=>255),
		));
	}

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

		$criteria->compareAs('first_name', $this->first_name, 'contact.first_name', true);
		$criteria->compareAs('last_name', $this->last_name, 'contact.last_name', true);
		$criteria->compareAs('email', $this->email, 'contact.email', true);
		$criteria->compareAs('phone_mobile', $this->phone_mobile, 'contact.phone_mobile', true);
		$criteria->compareAs('phone_home', $this->phone_home, 'contact.phone_home', true);
		$criteria->compareAs('phone_work', $this->phone_work, 'contact.phone_work', true);
		$criteria->compareAs('phone_fax', $this->phone_fax, 'contact.phone_fax', true);

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

	public function scopeClient($client_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('client_id', $client_id);

		$this->getDbCriteria()->mergeWith($criteria);
	
		return $this;
	}
	
}

?>