<?php

/**
 * This is the model class for table "standard_drawing".
 *
 * The followings are the available columns in table 'standard_drawing':
 * @property integer $id
 * @property integer $store_id
 * @property string $description
 * @property string $alias
 * @property string integer $parent_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AssemblyToStandardDrawing[] $assemblyToStandardDrawings
 * @property Staff $staff
 * @property Store $store
 * @property StandardDrawing $parent
 * @property StandardDrawing[] $standardDrawings
 */
class StandardDrawing extends ActiveRecord
{
//	public $file;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'standard_drawing';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, description, staff_id', 'required'),
			array('parent_id, store_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description alias, comment', 'length', 'max'=>255),
//			array('file', 'file', 'types'=>'jpg, gif, png, pdf', 'allowEmpty' => true),
			array('id, store_id, parent_id, description, comment, deleted, alias, searchStaff', 'safe', 'on'=>'search'),
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
			'assemblyToStandardDrawings' => array(self::HAS_MANY, 'AssemblyToStandardDrawing', 'standard_drawing_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'store' => array(self::BELONGS_TO, 'Store', 'store_id'),
			'parent' => array(self::BELONGS_TO, 'StandardDrawing', 'parent_id'),
			'standardDrawings' => array(self::HAS_MANY, 'StandardDrawing', 'parent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'store_id' => 'Store',
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
			't.description',
			't.parent_id',
			't.alias',
		);

		// where
		$criteria->compare('t.description', $this->description, true);
		$criteria->compare('t.alias', $this->alias,true);
		$criteria->compare('t.store_id', $this->store_id);
		$criteria->compareNull('t.parent_id',$this->parent_id);

		return $criteria;
	}

	public static function getDisplayAttr()
	{
		return array(
			'parent_id',
			'description',
			'alias',
		);
	}
 
	public function getAdminColumns()
	{
 		// link to admin displaying children or if no children then just description without link
        $this->linkColumnAdjacencyList('description', $columns);
		$columns[] = 'alias';
		
		return $columns;
	}

	public function scopeStore($store_id)
	{
		$criteria=new DbCriteria;
		$criteria->compare('store_id', $store_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	public function scopeAssembly($assembly_id)
	{
		$assembly = Assembly::model()->findByPk($assembly_id);
		
		return $this->scopeStore($assembly->store_id);
	}

}

?>