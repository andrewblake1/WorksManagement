<?php

/**
 * This is the model class for table "genericprojectcategory".
 *
 * The followings are the available columns in table 'genericprojectcategory':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $description
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericProjectType[] $genericProjectTypes
 * @property Staff $staff
 */
class Genericprojectcategory extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Project category';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'genericprojectcategory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('lft, rgt, level, description, staff_id', 'required'),
			array('root, lft, rgt, level, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, root, lft, rgt, level, description, searchStaff', 'safe', 'on'=>'search'),
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
			'genericProjectTypes' => array(self::HAS_MANY, 'GenericProjectType', 'genericprojectcategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project category',
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('t.root',$this->root);
		$criteria->compare('t.lft',$this->lft);
		$criteria->compare('t.rgt',$this->rgt);
		$criteria->compare('t.level',$this->level);
		$criteria->compare('t.description',$this->description,true);

		$criteria->select=array(
			't.root',
			't.lft',
			't.rgt',
			't.level',
			't.description',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'root';
		$columns[] = 'lft';
		$columns[] = 'rgt';
		$columns[] = 'level';
		$columns[] = 'description';
 		
		return $columns;
	}

}

?>