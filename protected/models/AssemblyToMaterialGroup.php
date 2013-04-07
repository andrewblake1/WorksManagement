<?php

/**
 * This is the model class for table "assembly_to_material_group".
 *
 * The followings are the available columns in table 'assembly_to_material_group':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $material_group_id
 * @property integer $stage_id
 * @property integer $store_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $comment
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Staff $staff
 * @property Stage $stage
 * @property Assembly $assembly
 * @property MaterialGroup $store
 * @property MaterialGroup $materialGroup
 */
class AssemblyToMaterialGroup extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterialGroupDescription;
	public $searchStage;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Material group';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, material_group_id, stage_id, store_id, quantity', 'required'),
			array('assembly_id, material_group_id, stage_id, store_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			array('select', 'safe'),
			array('id, assembly_id, searchStage, searchMaterialGroupDescription, quantity, minimum, maximum, comment, select', 'safe', 'on'=>'search'),
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
			'stage' => array(self::BELONGS_TO, 'Stage', 'stage_id'),
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
			'store' => array(self::BELONGS_TO, 'MaterialGroup', 'store_id'),
			'materialGroup' => array(self::BELONGS_TO, 'MaterialGroup', 'material_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'assembly_id' => 'Assembly',
			'material_group_id' => 'Material group/Stage',
			'searchMaterialGroupDescription' => 'Material group',
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
			't.material_group_id',
			'materialGroup.description AS searchMaterialGroupDescription',
	//		't.material_id',
			't.select',
			't.comment',
			't.quantity',
			't.minimum',
			't.maximum',
		);

		$criteria->compare('materialGroup.description',$this->searchMaterialGroupDescription,true);
		$criteria->compare('stage.description',$this->searchStage,true);
		$criteria->compare('t.assembly_id',$this->assembly_id);
		$criteria->compare('t.assembly_id',$this->assembly_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.minimium',$this->minimum);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('t.comment',$this->comment,true);
		$criteria->compare('t.select',$this->select,true);
		
		$criteria->with = array(
			'materialGroup',
			'stage',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('searchMaterialGroupDescription');
 		$columns[] = 'searchStage';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'comment';
 		$columns[] = 'select';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchMaterialGroupDescription',
			'searchStage',
		);
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'materialGroup->description',
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