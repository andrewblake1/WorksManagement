<?php

/**
 * This is the model class for table "tbl_client_contact".
 *
 * The followings are the available columns in table 'tbl_client_contact':
 * @property integer $id
 * @property integer $client_id
 * @property string $first_name
 * @property string $last_name
 * @property string $role
 * @property string $email
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $post_code
 * @property string $town_city
 * @property string $state_province
 * @property string $country
 * @property string $phone_mobile
 * @property string $phone_home
 * @property string $phone_work
 * @property string $phone_fax
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
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
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, first_name, last_name, email', 'required'),
			array('client_id', 'numerical', 'integerOnly'=>true),
			array('first_name, last_name, role, town_city, state_province, country, phone_mobile, phone_home, phone_work, phone_fax', 'length', 'max'=>64),
			array('email, address_line_1, address_line_2', 'length', 'max'=>255),
			array('post_code', 'length', 'max'=>16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, first_name, last_name, role, email, address_line_1, address_line_2, post_code, town_city, state_province, country, phone_mobile, phone_home, phone_work, phone_fax', 'safe', 'on'=>'search'),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
            'projectToClientContacts' => array(self::HAS_MANY, 'ProjectToClientContact', 'client_contact_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'client_id' => 'Client',
			'role' => 'Role',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.client_id',$this->client_id);
		$criteria->compare('t.first_name',$this->first_name,true);
		$criteria->compare('t.last_name',$this->last_name,true);
		$criteria->compare('t.role',$this->role,true);
		$criteria->compare('t.email',$this->email,true);
		$criteria->compare('t.phone_mobile',$this->phone_mobile,true);
		$criteria->compare('t.phone_home',$this->phone_home,true);
		$criteria->compare('t.phone_work',$this->phone_work,true);
		$criteria->compare('t.phone_fax',$this->phone_fax,true);

		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.first_name',
			't.last_name',
			't.role',
			't.email',
			't.phone_mobile',
			't.t.phone_home',
			'phone_work',
			't.phone_fax',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]=$this->linkThisColumn('first_name');
		$columns[]=$this->linkThisColumn('last_name');
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
			'first_name',
			'last_name',
			'email',
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