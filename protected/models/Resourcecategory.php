<?php

/**
 * This is the model class for table "resourcecategory".
 *
 * The followings are the available columns in table 'resourcecategory':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property integer $dutycategory_id
 * @property string $description
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ResourceType[] $resourceTypes
 * @property Dutycategory $dutycategory
 * @property Staff $staff
 */
class Resourcecategory extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Resource category';
	
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchDutycategory;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Resourcecategory the static model class
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
		return 'resourcecategory';
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
			array('root, lft, rgt, level, dutycategory_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, root, lft, rgt, level, searchDutycategory, description, deleted, searchStaff', 'safe', 'on'=>'search'),
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
			'resourceTypes' => array(self::HAS_MANY, 'ResourceType', 'resourcecategory_id'),
			'dutycategory' => array(self::BELONGS_TO, 'Dutycategory', 'dutycategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Resource category',
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'dutycategory_id' => 'Duty category',
			'searchDutycategory' => 'Duty category',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

//		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.root',$this->root);
		$criteria->compare('t.lft',$this->lft);
		$criteria->compare('t.rgt',$this->rgt);
		$criteria->compare('t.level',$this->level);
		$criteria->compare('dutycategory.description',$this->searchDutycategory);
		$criteria->compare('t.description',$this->description,true);
		
		$criteria->with = array('dutycategory');

		$criteria->select=array(
//			't.id',
			't.root',
			't.lft',
			't.rgt',
			't.level',
			'dutycategory.description AS searchDutycategory',
			't.description',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
//		$columns[] = 'id';
		$columns[] = 'root';
		$columns[] = 'lft';
		$columns[] = 'rgt';
		$columns[] = 'level';
        $columns[] = array(
			'name'=>'searchDutycategory',
			'value'=>'CHtml::link($data->searchDutycategory,
				Yii::app()->createUrl("Dutycategory/update", array("id"=>$data->dutycategory_id))
			)',
			'type'=>'raw',
		);
		$columns[] = 'description';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchDutycategory');
	}
}

?>