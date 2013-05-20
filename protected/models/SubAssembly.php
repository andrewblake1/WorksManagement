<?php

/**
 * This is the model class for table "tbl_sub_assembly".
 *
 * The followings are the available columns in table 'tbl_sub_assembly':
 * @property integer $id
 * @property integer $standard_id
 * @property integer $parent_assembly_id
 * @property integer $child_assembly_id
 * @property string $comment
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property Assembly $parentAssembly
 * @property Assembly $standard
 * @property Assembly $childAssembly
 * @property TaskToAssembly[] $taskToAssemblies
 */
class SubAssembly extends ActiveRecord
{

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Sub assembly';

	public $searchChildAssembly;
	protected $defaultSort = array('childAssembly.description');

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('standard_id, parent_assembly_id, child_assembly_id, quantity', 'required'),
			array('standard_id, parent_assembly_id, child_assembly_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			array('quantity_tooltip, comment', 'length', 'max'=>255),
			array('select', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('searchChildAssembly, id, parent_assembly_id, child_assembly_id, quantity, minimum, maximum, quantity_tooltip, select', 'safe', 'on'=>'search'),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'parentAssembly' => array(self::BELONGS_TO, 'Assembly', 'parent_assembly_id'),
            'standard' => array(self::BELONGS_TO, 'Assembly', 'standard_id'),
            'childAssembly' => array(self::BELONGS_TO, 'Assembly', 'child_assembly_id'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'sub_assembly_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'standard_id' => 'Standard',
			'parent_assembly_id' => 'Parent assembly',
			'child_assembly_id' => 'Sub assembly',
			'searchChildAssembly' => 'Child assembly',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',
			't.parent_assembly_id',
			't.child_assembly_id',
			"CONCAT_WS('$delimiter',
				childAssembly.description,
				childAssembly.alias
				) AS searchChildAssembly",
			't.select',
			't.quantity_tooltip',
			't.quantity',
			't.minimum',
			't.maximum',
			't.comment',
		);

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.parent_assembly_id',$this->parent_assembly_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.minimium',$this->minimum);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('t.quantity_tooltip',$this->quantity_tooltip,true);
		$criteria->compare('t.select',$this->select,true);
		$criteria->compare('t.comment',$this->comment,true);
		$this->compositeCriteria($criteria,
			array(
			'childAssembly.description',
			'childAssembly.alias'
			),
			$this->searchChildAssembly
		);

		$criteria->with = array(
			'childAssembly',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchChildAssembly', 'Assembly', 'child_assembly_id');
  		$columns[] = 'comment';
		$columns[] = 'quantity';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'select';
 		$columns[] = 'quantity_tooltip';
		
		return $columns;
	}

	public static function getDisplayAttr()
	{
		return array(
			'parentAssembly->description',
			'comment',
			'parentAssembly->alias',
		);
	}
 
	/**
	 * Returns foreign key attribute name within this model that references another model.
	 * @param string $referencesModel the name name of the model that the foreign key references.
	 * @return string the foreign key attribute name within this model that references another model
	 */
	static function getParentForeignKey($referencesModel)
	{
		return parent::getParentForeignKey($referencesModel, array('Assembly'=>'parent_assembly_id'));
	}
	
	public function scopeStandard($standard_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('standard_id', $standard_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}

	public function beforeValidate()
	{
		$this->standard_id = $this->parentAssembly->standard_id;
		
		return parent::beforeValidate();
	}

}