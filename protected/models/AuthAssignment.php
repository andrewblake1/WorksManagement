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
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property ProjectToAuthItemToAuthAssignment[] $projectToAuthItemToAuthAssignments
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

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return get_class($this);
	}
	
	public function scopes() {
		parent::scopes();
	}
	
	public function scopeProjectToAuthItemId($projectToAuthItemId)
	{
		$criteria=new DbCriteria;
		$criteria->compare('projectToAuthItemToAuthAssignment.project_to_auth_item_id', $projectToAuthItemId);
		$criteria->join='
			JOIN tbl_project_to_auth_item_to_auth_assignment projectToAuthItemToAuthAssignment ON t.id = projectToAuthItemToAuthAssignment.auth_assignment.id
		';

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('itemname, userid', 'required'),
			array('userid', 'numerical', 'integerOnly'=>true),
			array('itemname', 'length', 'max'=>64),
			array('bizrule, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, itemname, userid, searchUser, bizrule, data', 'safe', 'on'=>'search'),
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
			// BEWARE : gii missing a couple here!
			'user' => array(self::BELONGS_TO, 'User', 'userid'),
			'itemname0' => array(self::BELONGS_TO, 'AuthItem', 'itemname'),
           'projectToAuthItemToAuthAssignments' => array(self::HAS_MANY, 'ProjectToAuthItemToAuthAssignment', 'auth_assignment_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'itemname' => 'Role',
			'userid' => 'User, First/Last/Email',
			'searchUser' => 'User, First/Last/Email',
			'bizrule' => 'Bizrule',
			'data' => 'Data',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',
			't.itemname',
			't.userid',
		);

		// where
		$criteria->compare('t.itemname',$this->itemname,true);
		$criteria->compare('t.userid', $this->userid,true);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('itemname');
 		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='itemname';

		// NB: this clause needed to add the extra name stuff as this used in two places and user needed on ProjectToProjectTemplateToAuthItem
		if(!isset($_GET['userid']))
		{
			static::$labelOverrides['auth_assignment_id'] = 'Role/First/Last/Email';
//			$displaAttr[]='user->contact->first_name';
//			$displaAttr[]='user->contact->last_name';
//			$displaAttr[]='user->contact->email';
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
		return parent::getParentForeignKey($referencesModel, array('User'=>'userid'));
	}

}

?>