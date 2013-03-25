<?php

/**
 * This is the model class for table "report_to_AuthItem".
 *
 * The followings are the available columns in table 'report_to_AuthItem':
 * @property string $id
 * @property string $report_id
 * @property string $AuthItem_name
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Report $report
 * @property AuthItem $authItemName
 * @property Staff $staff
 */
class ReportToAuthItem extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Role';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'report_to_AuthItem';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('report_id, AuthItem_name', 'required'),
			array('report_id', 'length', 'max'=>10),
			array('AuthItem_name', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, report_id, AuthItem_name', 'safe', 'on'=>'search'),
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
			'report' => array(self::BELONGS_TO, 'Report', 'report_id'),
			'authItemName' => array(self::BELONGS_TO, 'AuthItem', 'AuthItem_name'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Report to role',
			'report_id' => 'Report',
			'AuthItem_name' => 'Role',
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
			't.id',	// needed for delete and update buttons
			'AuthItem_name',
		);

		// where
		$criteria->compare('t.AuthItem_name',$this->AuthItem_name,true);
		$criteria->compare('t.report_id',$this->report_id);
		
		return $criteria;
	}
	
	public function getAdminColumns()
	{
		$columns[] = $this->linkThisColumn('AuthItem_name');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		$displaAttr[]='AuthItem_name';

		return $displaAttr;
	}

}