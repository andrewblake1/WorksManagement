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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('client_id, first_name, last_name, email', 'required'),
			array('client_id', 'numerical', 'integerOnly'=>true),
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
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.role',
			'contact.first_name AS first_name',
			'contact.last_name AS last_name',
			'contact.email AS email',
			'contact.phone_mobile AS phone_mobile',
			'contact.phone_home AS phone_home',
			'contact.phone_work AS phone_work',
			'contact.phone_fax AS phone_fax',
		);

		// where
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.role',$this->role,true);
		$criteria->compare('contact.first_name',$this->first_name,true);
		$criteria->compare('contact.last_name',$this->last_name,true);
		$criteria->compare('contact.email',$this->email,true);
		$criteria->compare('contact.phone_mobile',$this->phone_mobile,true);
		$criteria->compare('contact.phone_home',$this->phone_home,true);
		$criteria->compare('contact.phone_work',$this->phone_work,true);
		$criteria->compare('contact.phone_fax',$this->phone_fax,true);

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