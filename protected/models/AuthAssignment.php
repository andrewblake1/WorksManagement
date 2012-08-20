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
 * @property Staff $user
 * @property Staff $staff
 * @property AuthItem $itemname0
 * @property ProjectToAuthAssignment[] $projectToAuthAssignments
 */
class AuthAssignment extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchUser;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuthAssignment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
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
			array('id, itemname, userid, searchUser, bizrule, data, searchStaff', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'Staff', 'userid'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'itemname0' => array(self::BELONGS_TO, 'AuthItem', 'itemname'),
			'projectToAuthAssignments' => array(self::HAS_MANY, 'ProjectToAuthAssignment', 'AuthAssignment_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Role Assignment',
			'naturalKey' => 'User/Role (First/Last/Email/Role)',
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
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
//			't.id',
			't.itemname',
			"CONCAT_WS('$delimiter',
				user.first_name,
				user.last_name,
				user.email
				) AS searchUser",
			't.bizrule',
			't.data',
		);

		// where
//		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.itemname',$this->itemname,true);
		$criteria->compare('t.bizrule',$this->bizrule,true);
		$criteria->compare('t.data',$this->data,true);
		if(isset($this->userid))
		{
			$criteria->compare('userid', $this->userid);
		}
		else
		{
			$this->compositeCriteria($criteria,
				array(
					'user.first_name',
					'user.last_name',
					'user.email'
				),
				$this->searchUser
			);
			$criteria->select[]="CONCAT_WS('$delimiter',
				user.first_name,
				user.last_name,
				user.email
				) AS searchUser";
		}

		// join
		$criteria->with = array('user');

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = 'itemname';
 		if(!isset($this->userid))
		{
			$columns[] = array(
				'name'=>'searchUser',
				'value'=>'CHtml::link($data->searchUser,
					Yii::app()->createUrl("Staff/update", array("id"=>$data->userid))
				)',
				'type'=>'raw',
			);
		}
		$columns[] = 'bizrule';
		$columns[] = 'data';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$controller = ucfirst(Yii::app()->controller->id);
		
//		// show when not coming from parent
//		if(!isset($_GET[$controller]['staff_id'])  && !isset($_GET[$controller]['project_id']) && !isset($_GET[$_GET['model']]))
		// if this pk attribute has been passed in a higher crumb in the breadcrumb trail
		if(Yii::app()->getController()->primaryKeyInBreadCrumbTrail('AuthAssignment_id'))
		{
			ActiveRecord::$labelOverrides['AuthAssignment_id'] = 'Role/First/Last/Email';
			$displaAttr[]='itemname';
			$displaAttr['user']='first_name';
			$displaAttr['user']='last_name';
			$displaAttr['user']='email';
		}
		else
		{
			ActiveRecord::$labelOverrides['AuthAssignment_id'] = 'Role';
			$displaAttr[]='itemname';
		}

		return $displaAttr;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchUser');
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