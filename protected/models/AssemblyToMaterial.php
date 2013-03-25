<?php

/**
 * This is the model class for table "assembly_to_material".
 *
 * The followings are the available columns in table 'assembly_to_material':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $material_id
 * @property integer $stage_id
 * @property integer $store_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $comment
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property Material $store
 * @property Material $material
 * @property Staff $staff
 * @property Stage $stage
 */
class AssemblyToMaterial extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterialDescription;
	public $searchMaterialUnit;
	public $searchMaterialAlias;
	public $searchStage;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, material_id, stage_id, store_id, quantity', 'required'),
			array('assembly_id, material_id, stage_id, store_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>255),
			array('id, assembly_id, searchStage, searchMaterialDescription, searchMaterialUnit, searchMaterialAlias, quantity, minimum, maximum, comment', 'safe', 'on'=>'search'),
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
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
			'store' => array(self::BELONGS_TO, 'Material', 'store_id'),
			'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'stage' => array(self::BELONGS_TO, 'Stage', 'stage_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'assembly_id' => 'Assembly',
			'material_id' => 'Material/Unit/Alias/Stage',
			'searchMaterialDescription' => 'Material',
			'searchMaterialUnit' => 'Unit',
			'searchMaterialAlias' => 'Alias',
			'stage_id' => 'Stage',
			'searchStage' => 'Stage',
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
			't.id',	// needed for delete and update buttons
			't.assembly_id',
			'stage.description AS searchStage',
			't.material_id',
			't.comment',
			'material.description AS searchMaterialDescription',
			'material.unit AS searchMaterialUnit',
			'material.alias AS searchMaterialAlias',
			't.quantity',
		);

		$criteria->compare('material.description',$this->searchMaterialDescription,true);
		$criteria->compare('stage.description',$this->searchStage,true);
		$criteria->compare('material.unit',$this->searchMaterialUnit,true);
		$criteria->compare('material.alias',$this->searchMaterialAlias,true);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.comment',$this->comment,true);
		$criteria->compare('t.assembly_id',$this->assembly_id);
		
		$criteria->with = array(
			'material',
			'stage',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('searchMaterialDescription');
 		$columns[] = 'comment';
 		$columns[] = 'searchMaterialUnit';
 		$columns[] = 'searchMaterialAlias';
 		$columns[] = 'searchStage';
 		$columns[] = 'quantity';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchMaterialDescription',
			'searchMaterialUnit',
			'searchMaterialAlias',
			'searchStage',
		);
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'material->description',
			'material->unit',
			'material->alias',
			'stage->description',
		);
	}
	
	public function beforeValidate()
	{
		$assembly = Assembly::model()->findByPk($this->assembly_id);
		$this->store_id = $assembly->store_id;
		
		return parent::beforeValidate();
	}

}