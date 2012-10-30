<?php

/**
 * This is the model class for table "client_contact".
 *
 * The followings are the available columns in table 'client_contact':
 * @property integer $id
 * @property integer $client_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $address_line1
 * @property string $address_line2
 * @property string $post_code
 * @property string $town_city
 * @property string $state_province
 * @property string $country
 * @property string $phone_mobile
 * @property string $phone_home
 * @property string $phone_work
 * @property string $phone_fax
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property Client $client
 * @property ProjectToClientContact[] $projectToClientContacts
 */
class ClientContact extends ActiveRecord
{

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Contact';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'client_contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, first_name, last_name, email, staff_id', 'required'),
			array('client_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('first_name, last_name, town_city, state_province, country, phone_mobile, phone_home, phone_work, phone_fax', 'length', 'max'=>64),
			array('email, address_line1, address_line2', 'length', 'max'=>255),
			array('post_code', 'length', 'max'=>16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, first_name, last_name, email, address_line1, address_line2, post_code, town_city, state_province, country, phone_mobile, phone_home, phone_work, phone_fax, deleted, staff_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'client_contact_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Client',
			'client_id' => 'Client',
			'first_name' => 'First name',
			'last_name' => 'Last name',
			'email' => 'Email',
			'address_line1' => 'Address line 1',
			'address_line2' => 'Address line 2',
			'post_code' => 'Post code',
			'town_city' => 'Town/city',
			'state_province' => 'State/province',
			'country' => 'Country',
			'phone_mobile' => 'Phone mobile',
			'phone_home' => 'Phone home',
			'phone_work' => 'Phone work',
			'phone_fax' => 'Phone fax',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('phone_mobile',$this->phone_mobile,true);
		$criteria->compare('phone_home',$this->phone_home,true);
		$criteria->compare('phone_work',$this->phone_work,true);
		$criteria->compare('phone_fax',$this->phone_fax,true);

		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'first_name',
			'last_name',
			'email',
			'phone_mobile',
			'phone_home',
			'phone_work',
			'phone_fax',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]=$this->linkThisColumn('first_name');
		$columns[]=$this->linkThisColumn('last_name');
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
			'first_name',
			'last_name',
			'email',
		);
	}
	
}

?>