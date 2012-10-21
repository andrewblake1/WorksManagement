<?php

/**
 * This is the model class for table "AuthItemChild".
 *
 * The followings are the available columns in table 'AuthItemChild':
 * @property integer $id
 * @property string $parent
 * @property string $child
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property AuthItem $child0
 * @property AuthItem $parent0
 */
class AuthItemChild extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Priviledge';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'AuthItemChild';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent, child, staff_id', 'required'),
			array('staff_id', 'numerical', 'integerOnly'=>true),
			array('parent, child', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parent, child, staff_id', 'safe', 'on'=>'search'),
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
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'child0' => array(self::BELONGS_TO, 'AuthItem', 'child'),
			'parent0' => array(self::BELONGS_TO, 'AuthItem', 'parent'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Auth item child',
			'parent' => 'Parent',
			'child' => 'Priveledge',
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
			't.child',
		);	

		// where
		$criteria->compare('t.parent',$this->parent);
		$criteria->compare('t.child',$this->child, true);

		return $criteria;
	}

	public function getAdminColumns()
	{
 		$columns[] = 'child';
		
		return $columns;
	}

	/**
	 * Returns foreign key attribute name within this model that references another model.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel)
	{
		return parent::getParentForeignKey($referencesModel, array('AuthItem'=>'parent'));
	}
	
}

?>