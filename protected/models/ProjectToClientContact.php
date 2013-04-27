<?php

/**
 * This is the model class for table "project_to_client_contact".
 *
 * The followings are the available columns in table 'project_to_client_contact':
 * @property integer $id
 * @property string $project_id
 * @property integer $client_id
 * @property integer $client_contact_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ClientContact $clientContact
 * @property Project $client
 * @property Staff $staff
 * @property Project $project
 */
class ProjectToClientContact extends ActiveRecord
{
	public $searchRole;
	public $searchFirst_name;
	public $searchLast_name;
	public $searchEmail;
	public $searchPhone_mobile;
	public $searchPhone_home;
	public $searchPhone_work;
	public $searchPhone_fax;

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
			array('project_id, client_contact_id', 'required'),
			array('client_id, client_contact_id', 'numerical', 'integerOnly'=>true),
			array('project_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchRole, searchFirst_name, searchLast_name, searchEmail, searchPhone_mobile, searchPhone_home, searchPhone_work, searchPhone_fax, project_id, client_id, client_contact_id', 'safe', 'on'=>'search'),
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
			'clientContact' => array(self::BELONGS_TO, 'ClientContact', 'client_contact_id'),
			'client' => array(self::BELONGS_TO, 'Project', 'client_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
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
			'searchFirst_name' => 'First name',
			'searchLast_name' => 'Last name',
			'searchEmail' => 'Email',
			'searchPhone_mobile' => 'Phone mobile',
			'searchPhone_home' => 'Phone home',
			'searchPhone_work' => 'Phone work',
			'searchPhone_fax' => 'Phone fax',
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
			'clientContact.first_name AS searchFirst_name',
			'clientContact.last_name AS searchLast_name',
			'clientContact.email AS searchEmail',
			'clientContact.phone_mobile AS searchPhone_mobile',
			'clientContact.phone_home AS searchPhone_home',
			'clientContact.phone_work AS searchPhone_work',
			'clientContact.phone_fax AS searchPhone_fax',
		);

		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('clientContact.role',$this->searchRole,true);
		$criteria->compare('clientContact.first_name',$this->searchFirst_name,true);
		$criteria->compare('clientContact.last_name',$this->searchLast_name,true);
		$criteria->compare('clientContact.email',$this->searchEmail,true);
		$criteria->compare('clientContact.phone_mobile',$this->searchPhone_mobile,true);
		$criteria->compare('clientContact.phone_home',$this->searchPhone_home,true);
		$criteria->compare('clientContact.phone_work',$this->searchPhone_work,true);
		$criteria->compare('clientContact.phone_fax',$this->searchPhone_fax,true);

		// join
		$criteria->with = array(
			'clientContact',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[]='searchRole';
		$columns[]=$this->linkThisColumn('searchFirst_name');
		$columns[]=$this->linkThisColumn('searchLast_name');
        $columns[] = array(
			'name'=>'searchPhone_mobile',
			'value'=>'CHtml::link($data->searchPhone_mobile, "tel:".$data->searchPhone_mobile)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchPhone_home',
			'value'=>'CHtml::link($data->searchPhone_home, "tel:".$data->searchPhone_home)',
			'type'=>'raw',
		);
        $columns[] = array(
			'name'=>'searchPhone_work',
			'value'=>'CHtml::link($data->searchPhone_work, "tel:".$data->searchPhone_work)',
			'type'=>'raw',
		);
		$columns[]='searchPhone_fax';
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
			'clientContact->first_name',
			'clientContact->last_name',
			'clientContact->email',
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
	
	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchFirst_name',
			'searchLast_name',
			'searchEmail',
			'searchPhone_mobile',
			'searchPhone_home',
			'searchPhone_work',
			'searchPhone_fax',
		);
	}

}

?>