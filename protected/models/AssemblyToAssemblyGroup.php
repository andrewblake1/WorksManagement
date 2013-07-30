<?php

/**
 * This is the model class for table "tbl_assembly_to_assembly_group".
 *
 * The followings are the available columns in table 'tbl_assembly_to_assembly_group':
 * @property string $id
 * @property integer $assembly_id
 * @property integer $standard_id
 * @property integer $assembly_group_id
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
 * @property AssemblyGroup $standard
 * @property AssemblyGroup $assemblyGroup
 * @property User $updatedBy
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups
 */
class AssemblyToAssemblyGroup extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssemblyGroup;
	public $searchDetailDrawing;

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
            'standard' => array(self::BELONGS_TO, 'AssemblyGroup', 'standard_id'),
            'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroup', 'assembly_group_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskToAssemblyToAssemblyToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'assembly_to_assembly_group_id'),
        );
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
			't.assembly_group_id',
			'assemblyGroup.description AS searchAssemblyGroup',
			"CONCAT_WS('$delimiter',
				detailDrawing.alias,
				detailDrawing.description
				) AS searchDetailDrawing",
			't.detail_drawing_id',
			't.select',
			't.quantity_tooltip',
			't.selection_tooltip',
			't.comment',
			't.quantity',
			't.minimum',
			't.maximum',
		);

		$criteria->compare('assemblyGroup.description',$this->searchAssemblyGroup,true);
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
			$this->searchDetailDrawing
		);

		
		$criteria->with = array(
			'assemblyGroup',
			'detailDrawing',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = 'searchAssemblyGroup';
 		$columns[] = 'comment';
		$columns[] = static::linkColumn('searchDetailDrawing', 'Drawing', 'detail_drawing_id');
 		$columns[] = 'selection_tooltip';
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
			'searchAssemblyGroup',
			'comment',
		);
	}
	
	public function beforeValidate()
	{
		$assembly = Assembly::model()->findByPk($this->assembly_id);
		$this->standard_id = $assembly->standard_id;
		
		return parent::beforeValidate();
	}

}