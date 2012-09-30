<?php

/**
 * This is the model class for table "AuthItem".
 *
 * The followings are the available columns in table 'AuthItem':
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $bizrule
 * @property string $data
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AuthAssignment[] $authAssignments
 * @property Staff $staff
 * @property AuthItemChild[] $authItemchildren
 * @property AuthItemChild[] $authItemchildren1
 * @property ProjectTypeToAuthItem[] $projectTypeToAuthItems
 * @property ReportToAuthItem[] $reportToAuthItems
 */
class AuthItem extends ActiveRecord
{
	/**
	 *  Auth item type constants
	 */
	const typeRole = 2;
	const typeRight = 1;
	const typeTask = 1;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	public function scopes()
    {
		return array(
			'roles'=>array('condition'=>'t.type=' . self::typeRole),
			'rights'=>array('condition'=>'t.type=' . self::typeRight),
			'tasks'=>array('condition'=>'t.type=' . self::typeTask),
		);
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'AuthItem';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>64),
			array('description, bizrule, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, type, description, bizrule, data, searchStaff', 'safe', 'on'=>'search'),
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
			'authAssignments' => array(self::HAS_MANY, 'AuthAssignment', 'itemname'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'authItemchildren' => array(self::HAS_MANY, 'AuthItemChild', 'child'),
			'authItemchildren1' => array(self::HAS_MANY, 'AuthItemChild', 'parent'),
			'projectTypeToAuthItems' => array(self::HAS_MANY, 'ProjectTypeToAuthItem', 'AuthItem_name'),
			'reportToAuthItems' => array(self::HAS_MANY, 'ReportToAuthItem', 'AuthItem_name'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'name' => 'Role',
			'type' => 'Type',
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

		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.type', self::typeRole);
		$criteria->compare('t.description',$this->description,true);

		$criteria->select=array(
			't.name',
			't.description',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'name';
		$columns[] = 'description';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
//			'name',
			'description',
		);
	}

	/*
	 * can't set default value in database as TEXT data type but is required
	 */
	public function init()
	{
		// can't set default value in database as TEXT data type for AuthItem
		$this->data = 'N;';
		$this->type = '2';
		
		parent::init();
	}

}

?>