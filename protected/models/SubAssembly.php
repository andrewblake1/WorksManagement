<?php

/**
 * This is the model class for table "tbl_sub_assembly".
 *
 * The followings are the available columns in table 'tbl_sub_assembly':
 * @property integer $id
 * @property integer $standard_id
 * @property integer $parent_assembly_id
 * @property integer $child_assembly_id
 * @property integer $detail_drawing_id
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
 * @property Drawing $detailDrawing
 * @property Assembly $parentAssembly
 * @property Assembly $standard
 * @property Assembly $childAssembly
 * @property User $updatedBy
 * @property TaskToAssembly[] $taskToAssemblies
 */
class SubAssembly extends ActiveRecord
{
	public $searchChildAssembly;
	public $searchDetailDrawing;

	protected $defaultSort = array('childAssembly.description');

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'detailDrawing' => array(self::BELONGS_TO, 'Drawing', 'detail_drawing_id'),
            'parentAssembly' => array(self::BELONGS_TO, 'Assembly', 'parent_assembly_id'),
            'standard' => array(self::BELONGS_TO, 'Assembly', 'standard_id'),
            'childAssembly' => array(self::BELONGS_TO, 'Assembly', 'child_assembly_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskToAssemblies' => array(self::HAS_MANY, 'TaskToAssembly', 'sub_assembly_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'child_assembly_id' => 'Sub assembly',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria($this, array('parent_assembly_id'));

		$criteria->compareNull('t.parent_assembly_id',$this->parent_assembly_id);
		$criteria->composite('searchChildAssembly', $this->searchChildAssembly, array(
			'childAssembly.description',
			'childAssembly.alias'
		));
		$criteria->composite('searchDetailDrawing', $this->searchDetailDrawing, array(
			'detailDrawing.alias',
			'detailDrawing.description'
		));

		$criteria->with = array(
			'childAssembly',
			'detailDrawing',
		);

		return $criteria;
	}
	
	public function getAdminColumns()
	{
        $columns[] = 'searchChildAssembly';
  		$columns[] = 'comment';
		$columns[] = static::linkColumn('searchDetailDrawing', 'Drawing', 'detail_drawing_id');
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
			'searchChildAssembly',
			'comment',
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