<?php

/**
 * This is the model class for table "AuthItemChild".
 *
 * The followings are the available columns in table 'AuthItemChild':
 * @property integer $id
 * @property string $parent
 * @property string $child
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property AuthItem $parent0
 * @property AuthItem $child0
 * @property User $updatedBy
 */
class AuthItemChild extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Privilege';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return get_class($this);
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent, child', 'required'),
			array('parent, child', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parent, child', 'safe', 'on'=>'search'),
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
            'parent0' => array(self::BELONGS_TO, 'AuthItem', 'parent'),
            'child0' => array(self::BELONGS_TO, 'AuthItem', 'child'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
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
 		$columns[] = $this->linkThisColumn('child');
		
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
	
	public static function getDisplayAttr()
	{
		return array(
			'parent',
			'child',
		);
	}
 
}

?>