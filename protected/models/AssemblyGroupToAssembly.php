<?php

/**
 * This is the model class for table "assembly_group_to_assembly".
 *
 * The followings are the available columns in table 'assembly_group_to_assembly':
 * @property integer $id
 * @property integer $assembly_group_id
 * @property integer $assembly_id
 * @property integer $store_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AssemblyGroup $assemblyGroup
 * @property Assembly $store
 * @property Assembly $assembly
 * @property Staff $staff
 * @property TaskToAssemblyToAssemblyGroupToAssembly[] $taskToAssemblyToAssemblyGroupToAssemblys
 * @property TaskToAssemblyToAssemblyGroupToAssembly[] $taskToAssemblyToAssemblyGroupToAssemblys1
 */
class AssemblyGroupToAssembly extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssembly;

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Assembly';
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assembly_id, assembly_group_id, store_id,', 'required'),
			array('assembly_id, assembly_group_id, store_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchAssembly, assembly_id, assembly_group_id', 'safe', 'on'=>'search'),
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
			'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroup', 'assembly_group_id'),
			'store' => array(self::BELONGS_TO, 'Assembly', 'store_id'),
			'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'taskToAssemblyToAssemblyGroupToAssemblys' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyGroupToAssembly', 'assembly_id'),
			'taskToAssemblyToAssemblyGroupToAssemblys1' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyGroupToAssembly', 'assembly_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'assembly_group_id' => 'Assembly Group',
			'assembly_id' => 'Assembly',
			'searchAssembly' => 'Assembly',
		);
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			'assembly.description AS searchAssembly',
		);

		// where
		$criteria->compare('assembly_group_id',$this->assembly_group_id);
		$criteria->compare('assembly.description',$this->searchAssembly);

		// join
		$criteria->with = array(
			'assembly',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
        $columns[] = static::linkColumn('searchAssembly', 'Assembly', 'assembly_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'assembly->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchAssembly');
	}
	
	public function beforeValidate()
	{
		$assemblyGroup = AssemblyGroup::model()->findByPk($this->assembly_group_id);
		$this->store_id = $assemblyGroup->store_id;
		
		return parent::beforeValidate();
	}

}