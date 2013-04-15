<?php

/**
 * This is the model class for table "assembly_to_assembly_group".
 *
 * The followings are the available columns in table 'assembly_to_assembly_group':
 * @property string $id
 * @property integer $assembly_id
 * @property integer $store_id
 * @property integer $assembly_group_id
 * @property integer $quantity
 * @property integer $minimum
 * @property integer $maximum
 * @property string $select
 * @property string $quantity_tooltip
 * @property string $selection_tooltip
 * @property string $comment
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly $assembly
 * @property AssemblyGroup $store
 * @property AssemblyGroup $assemblyGroup
 * @property Staff $staff
 * @property TaskToAssemblyToAssemblyGroupToAssembly[] $taskToAssemblyToAssemblyGroupToAssemblies
 * @property TaskToAssemblyToAssemblyGroupToAssembly[] $taskToAssemblyToAssemblyGroupToAssemblies1
 */
class AssemblyToAssemblyGroup extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssemblyGroupDescription;
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly group';

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, assembly_group_id, store_id, quantity', 'required'),
			array('assembly_id, assembly_group_id, store_id, quantity, minimum, maximum', 'numerical', 'integerOnly'=>true),
			array('quantity_tooltip, selection_tooltip, comment', 'length', 'max'=>255),
			array('select', 'safe'),
			array('id, assembly_id, searchAssemblyGroupDescription, quantity, minimum, maximum, quantity_tooltip, selection_tooltip, select', 'safe', 'on'=>'search'),
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
			'store' => array(self::BELONGS_TO, 'AssemblyGroup', 'store_id'),
			'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroup', 'assembly_group_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToAssemblyToAssemblyGroupToAssemblies' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyGroupToAssembly', 'assembly_group_id'),
			'taskToAssemblyToAssemblyGroupToAssemblies1' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyGroupToAssembly', 'assembly_to_assembly_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'assembly_id' => 'Assembly',
			'assembly_group_id' => 'Assembly group',
			'searchAssemblyGroupDescription' => 'Assembly group',
			'quantity_tooltip' => 'Quantity tooltip',
			'selection_tooltip' => 'Selection tooltip',
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
			't.assembly_group_id',
			'assemblyGroup.description AS searchAssemblyGroupDescription',
			't.select',
			't.quantity_tooltip',
			't.selection_tooltip',
			't.comment',
			't.quantity',
			't.minimum',
			't.maximum',
		);

		$criteria->compare('assemblyGroup.description',$this->searchAssemblyGroupDescription,true);
		$criteria->compare('t.assembly_id',$this->assembly_id);
		$criteria->compare('t.assembly_id',$this->assembly_id);
		$criteria->compare('t.quantity',$this->quantity);
		$criteria->compare('t.minimium',$this->minimum);
		$criteria->compare('t.maximum',$this->maximum);
		$criteria->compare('t.select',$this->select,true);
		$criteria->compare('t.quantity_tooltip',$this->quantity_tooltip,true);
		$criteria->compare('t.selection_tooltip',$this->selection_tooltip,true);
		$criteria->compare('t.comment',$this->comment,true);
		
		$criteria->with = array(
			'assemblyGroup',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = $this->linkThisColumn('searchAssemblyGroupDescription');
 		$columns[] = 'quantity';
 		$columns[] = 'minimum';
 		$columns[] = 'maximum';
 		$columns[] = 'select';
 		$columns[] = 'quantity_tooltip';
 		$columns[] = 'selection_tooltip';
 		$columns[] = 'comment';
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchAssemblyGroupDescription',
		);
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'assemblyGroup->description',
			'comment',
		);
	}
	
	public function beforeValidate()
	{
		$assembly = Assembly::model()->findByPk($this->assembly_id);
		$this->store_id = $assembly->store_id;
		
		return parent::beforeValidate();
	}

}