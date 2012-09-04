<?php

/**
 * This is the model class for table "AuthAssignment".
 *
 * The followings are the available columns in table 'AuthAssignment':
 * @property integer $id
 * @property string $itemname
 * @property integer $userid
 * @property string $bizrule
 * @property string $data
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AuthItem $itemname0
 * @property Staff $user
 * @property Staff $staff
 * @property ProjectToProjectTypeToAuthItem[] $projectToProjectTypeToAuthItems
 */
class AuthAssignment extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchUser;

	public function scopeProjectToProjectTypeToAuthItem($project_id)
	{
		$criteria=new CDbCriteria;
		$criteria->compare('project.id',$project_id);
		$criteria->join='
			JOIN project_type_to_AuthItem projectTypeToAuthItem ON t.itemname=projectTypeToAuthItem.AuthItem_name
			JOIN project USING(project_type_id)
		';

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'AuthAssignment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('itemname, userid, staff_id', 'required'),
			array('userid, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('itemname', 'length', 'max'=>64),
			array('bizrule, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, staff_id, itemname, userid, searchUser, bizrule, data, searchStaff', 'safe', 'on'=>'search'),
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
			'itemname0' => array(self::BELONGS_TO, 'AuthItem', 'itemname'),
			'user' => array(self::BELONGS_TO, 'Staff', 'userid'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectToProjectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectToProjectTypeToAuthItem', 'AuthAssignment_id'),
			'reportToAuthItems' => array(self::HAS_MANY, 'ReportToAuthItem', array('name'=>'AuthItem_name'), 'through'=>'itemname0'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Role Assignment',
//			'naturalKey' => 'User/Role (First/Last/Email/Role)',
			'itemname' => 'Role',
			'userid' => 'User, First/Last/Email',
			'searchUser' => 'User, First/Last/Email',
			'bizrule' => 'Bizrule',
			'data' => 'Data',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$criteria->select=array(
			't.itemname',
		);

		// where
		$criteria->compare('t.itemname',$this->itemname,true);
		$criteria->compare('t.userid', $this->userid);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'itemname';
 		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='itemname';

		// NB: this clause needed to add the extra name stuff as this used in two places and user needed on ProjectToProjectTypeToAuthItem
		if(!isset($_GET['userid']))
		{
			static::$labelOverrides['AuthAssignment_id'] = 'Role/First/Last/Email';
			$displaAttr[]='user->first_name';
			$displaAttr[]='user->last_name';
			$displaAttr[]='user->email';
		}


		return $displaAttr;
	}

	/**
	 * Returns foreign key attribute name within this model that references another model.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel)
	{
		return parent::getParentForeignKey($referencesModel, array('Staff'=>'userid'));
	}

}

?>