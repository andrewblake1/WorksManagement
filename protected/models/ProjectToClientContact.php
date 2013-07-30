<?php

/**
 * This is the model class for table "tbl_project_to_client_contact".
 *
 * The followings are the available columns in table 'tbl_project_to_client_contact':
 * @property integer $id
 * @property string $project_id
 * @property integer $client_contact_id
 * @property integer $client_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property ClientContact $clientContact
 * @property Project $client
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
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'clientContact' => array(self::BELONGS_TO, 'ClientContact', 'client_contact_id'),
            'client' => array(self::BELONGS_TO, 'Project', 'client_id'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
        );
    }

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this);

		$criteria->compareAs('searchRole', $this->searchRole, 'clientContact.role', true);
		$criteria->compareAs('searchFirstName', $this->searchFirstName, 'contact.first_name', true);
		$criteria->compareAs('searchLastName', $this->searchLastName, 'contact.first_name', true);
		$criteria->compareAs('searchEmail', $this->searchEmail, 'contact.first_name', true);
		$criteria->compareAs('searchPhoneMobile', $this->searchPhoneMobile, 'contact.phone_mobile', true);
		$criteria->compareAs('searchPhoneHome', $this->searchPhoneHome, 'contact.phone_home', true);
		$criteria->compareAs('searchPhoneWork', $this->searchPhoneWork, 'contact.phone_work', true);
		$criteria->compareAs('searchPhoneFax', $this->searchPhoneFax, 'contact.phone_fax', true);

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
		$columns[]='searchFirstName';
		$columns[]='searchLastName';
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
			'searchFirstName',
			'searchLastName',
			'searchEmail',
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