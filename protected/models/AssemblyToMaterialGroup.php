<?php

/**
 * This is the model class for table "tbl_assembly_to_material_group".
 *
 * The followings are the available columns in table 'tbl_assembly_to_material_group':
 * @property integer $id
 * @property integer $assembly_id
 * @property integer $stage_id
 * @property integer $standard_id
 * @property integer $material_group_id
 * @property integer $detail_drawing_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property string $selection_tooltip
 * @property string $comment
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Drawing $detailDrawing
 * @property Assembly $assembly
 * @property MaterialGroup $standard
 * @property MaterialGroup $materialGroup
 * @property User $updatedBy
 * @property Stage $stage
 * @property TaskToMaterialToAssemblyToMaterialGroup[] $taskToMaterialToAssemblyToMaterialGroups
 */
class AssemblyToMaterialGroup extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterialGroupDescription;
	public $searchStage;
	public $searchDetailDrawingDescription;

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
		return array_merge(parent::rules(), array(
			array('assembly_id, material_group_id, stage_id, standard_id, quantity', 'required'),
			array('assembly_id, material_group_id, stage_id, standard_id, detail_drawing_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			array('select', 'safe'),
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
            'detailDrawing' => array(self::BELONGS_TO, 'Drawing', 'detail_drawing_id'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'standard' => array(self::BELONGS_TO, 'MaterialGroup', 'standard_id'),
            'materialGroup' => array(self::BELONGS_TO, 'MaterialGroup', 'material_group_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'stage' => array(self::BELONGS_TO, 'Stage', 'stage_id'),
            'taskToMaterialToAssemblyToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'assembly_to_material_group_id'),
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
			'quantity_tooltip' => 'Quantity tooltip',
			'selection_tooltip' => 'Selection tooltip',
			'searchStage' => 'Stage',
			'searchDetailDrawingDescription' => 'Detail drawing',
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
			"CONCAT_WS('$delimiter',
				detailDrawing.alias,
				detailDrawing.description
				) AS searchDetailDrawingDescription",
			't.detail_drawing_id',
			't.select',
			't.quantity_tooltip',
			't.selection_tooltip',
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
		$criteria->compare('t.select',$this->select,true);
		$criteria->compare('t.quantity_tooltip',$this->quantity_tooltip,true);
		$criteria->compare('t.selection_tooltip',$this->selection_tooltip,true);
		$criteria->compare('t.comment',$this->comment,true);
		$this->compositeCriteria($criteria,
			array(
				'detailDrawing.alias',
				'detailDrawing.description',
			),
			$this->searchDetailDrawingDescription
		);

		
		$criteria->with = array(
			'materialGroup',
			'stage',
			'detailDrawing',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('searchMaterialGroupDescription');
 		$columns[] = 'comment';
		$columns[] = static::linkColumn('searchDetailDrawingDescription', 'Drawing', 'detail_drawing_id');
 		$columns[] = 'selection_tooltip';
 		$columns[] = 'searchStage';
 		$columns[] = 'quantity';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'select';
 		$columns[] = 'quantity_tooltip';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'searchMaterialGroupDescription',
			'comment',
			'searchStage',
		);
	}
	
	public function beforeValidate()
	{
		$assembly = Assembly::model()->findByPk($this->assembly_id);
		$this->standard_id = $assembly->standard_id;
		
		return parent::beforeValidate();
	}

}