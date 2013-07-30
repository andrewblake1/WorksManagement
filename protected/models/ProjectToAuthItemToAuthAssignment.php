<?php

/**
 * This is the model class for table "tbl_project_to_auth_item_to_auth_assignment".
 *
 * The followings are the available columns in table 'tbl_project_to_auth_item_to_auth_assignment':
 * @property integer $id
 * @property string $project_to_auth_item_id
 * @property integer $auth_assignment_id
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ProjectToAuthItem $projectToAuthItem
 * @property AuthAssignment $authAssignment
 * @property User $updatedBy
 */
class ProjectToAuthItemToAuthAssignment extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'User';
	
	public $first_name;
	public $last_name;
	public $email;
	public $phone_mobile;
	public $phone_home;
	public $phone_work;
	public $phone_fax;
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'projectToAuthItem' => array(self::BELONGS_TO, 'ProjectToAuthItem', 'project_to_auth_item_id'),
			'authAssignment' => array(self::BELONGS_TO, 'AuthAssignment', 'auth_assignment_id'),
			'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'project_to_auth_item_id' => 'Role',
			'auth_assignment_id' => 'User',
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
		
		$criteria->with=array(
			'authAssignment.user.contact',
		);
		
		return $criteria;
	}
	
	public function getAdminColumns()
	{
		$columns[]='first_name';
		$columns[]='last_name';
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
//		$displaAttr[]='id';
		$displaAttr[]='first_name';
		$displaAttr[]='last_name';
		$displaAttr[]='email';

		return $displaAttr;
	}

}