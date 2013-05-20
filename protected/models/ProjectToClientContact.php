<?php

/**
 * This is the model class for table "tbl_project_to_client_contact".
 *
 * The followings are the available columns in table 'tbl_project_to_client_contact':
 * @property integer $id
 * @property string $project_id
 * @property integer $client_id
 * @property integer $client_contact_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ClientContact $clientContact
 * @property Project $client
 * @property User $updatedBy
 * @property Project $project
 */
class ProjectToClientContact extends ActiveRecord
{
	public $searchRole;
	public $searchFirstName;
	public $searchLastName;
	public $searchEmail;
	public $searchPhoneMobile;
	public $searchPhoneHome;
	public $searchPhoneWork;
	public $searchPhoneFax;

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
		return array_merge(parent::rules(), array(
			array('project_id, client_contact_id', 'required'),
			array('client_id, client_contact_id', 'numerical', 'integerOnly'=>true),
			array('project_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, searchRole, searchFirstName, searchLastName, searchEmail, searchPhoneMobile, searchPhoneHome, searchPhoneWork, searchPhoneFax, project_id, client_id, client_contact_id', 'safe', 'on'=>'search'),
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
            'clientContact' => array(self::BELONGS_TO, 'ClientContact', 'client_contact_id'),
            'client' => array(self::BELONGS_TO, 'Project', 'client_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'project_id' => 'Project',
			'client_id' => 'Client',
			'client_contact_id' => 'Contact',
			'searchRole' => 'Role',
			'searchFirstName' => 'First name',
			'searchLastName' => 'Last name',
			'searchEmail' => 'Email',
			'searchPhoneMobile' => 'Phone mobile',
			'searchPhoneHome' => 'Phone home',
			'searchPhoneWork' => 'Phone work',
			'searchPhoneFax' => 'Phone fax',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.client_contact_id',
			'clientContact.role AS searchRole',
			'contact.first_name AS searchFirstName',
			'contact.last_name AS searchLastName',
			'contact.email AS searchEmail',
			'contact.phone_mobile AS searchPhoneMobile',
			'contact.phone_home AS searchPhoneHome',
			'contact.phone_work AS searchPhoneWork',
			'contact.phone_fax AS searchPhoneFax',
		);

		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('clientContact.role',$this->searchRole,true);
		$criteria->compare('contact.first_name',$this->searchFirstName,true);
		$criteria->compare('contact.last_name',$this->searchLastName,true);
		$criteria->compare('contact.email',$this->searchEmail,true);
		$criteria->compare('contact.phone_mobile',$this->searchPhoneMobile,true);
		$criteria->compare('contact.phone_home',$this->searchPhoneHome,true);
		$criteria->compare('contact.phone_work',$this->searchPhoneWork,true);
		$criteria->compare('contact.phone_fax',$this->searchPhoneFax,true);

		// with
		$criteria->with = array(
			'clientContact',
			'clientContact.contact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]='searchRole';
		$columns[]=$this->linkThisColumn('searchFirstName');
		$columns[]=$this->linkThisColumn('searchLastName');
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
        $columns[] = array(
			'name'=>'searchEmail',
			'value'=>'$data->searchEmail',
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
			'clientContact->contact->first_name',
			'clientContact->contact->last_name',
			'clientContact->contact->email',
		);
	}

	public function beforeSave() {
		if(!empty($this->client_contact_id))
		{
			$clientContact = ClientContact::model()->findByPk($this->client_contact_id);
			$this->client_id = $clientContact->client_id;
		}
		return parent::beforeSave();
	}
	
}

?>