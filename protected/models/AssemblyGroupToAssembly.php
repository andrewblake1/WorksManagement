<?php

/**
 * This is the model class for table "tbl_assembly_group_to_assembly".
 *
 * The followings are the available columns in table 'tbl_assembly_group_to_assembly':
 * @property string $id
 * @property integer $assembly_group_id
 * @property integer $assembly_id
 * @property integer $store_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property User $updatedBy
 * @property AssemblyGroup $assemblyGroup
 * @property Assembly $store
 * @property Assembly $assembly
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups1
 * @property TaskToAssemblyToAssemblyToAssemblyGroup[] $taskToAssemblyToAssemblyToAssemblyGroups2
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups
 * @property TaskToAssemblyToTaskTemplateToAssemblyGroup[] $taskToAssemblyToTaskTemplateToAssemblyGroups1
 */
class AssemblyGroupToAssembly extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchAssemblyDescription;
	public $searchAssemblyAlias;

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
			array('assembly_id, assembly_group_id, standard_id,', 'required'),
			array('assembly_id, assembly_group_id, standard_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchAssemblyDescription, searchAssemblyAlias, assembly_id, assembly_group_id', 'safe', 'on'=>'search'),
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
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'assemblyGroup' => array(self::BELONGS_TO, 'AssemblyGroup', 'assembly_group_id'),
            'store' => array(self::BELONGS_TO, 'Assembly', 'store_id'),
            'assembly' => array(self::BELONGS_TO, 'Assembly', 'assembly_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'assembly_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups1' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'assembly_group_id'),
            'taskToAssemblyToAssemblyToAssemblyGroups2' => array(self::HAS_MANY, 'TaskToAssemblyToAssemblyToAssemblyGroup', 'assembly_group_to_assembly_id'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'assembly_id'),
            'taskToAssemblyToTaskTemplateToAssemblyGroups1' => array(self::HAS_MANY, 'TaskToAssemblyToTaskTemplateToAssemblyGroup', 'assembly_group_to_assembly_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'assembly_group_id' => 'Assembly Group',
//			'assembly_id' => 'Assembly',
			'searchAssemblyDescription' => 'Assembly',
			'searchAssemblyAlias' => 'Alias',
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
			'assembly.description AS searchAssemblyDescription',
			'assembly.alias AS searchAssemblyAlias',
		);

		// where
		$criteria->compare('assembly_group_id',$this->assembly_group_id);
		$criteria->compare('assembly.description',$this->searchAssemblyDescription,true);
		$criteria->compare('assembly.alias',$this->searchAssemblyAlias,true);

		// with
		$criteria->with = array(
			'assembly',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchAssemblyDescription';
 		$columns[] = 'searchAssemblyAlias';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'assemblyGroup->description',
			'assembly->description',
			'assembly->alias',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array(
			'searchAssemblyDescription',
			'searchAssemblyAlias',
		);
	}
	
	public function beforeValidate()
	{
		$assemblyGroup = AssemblyGroup::model()->findByPk($this->assembly_group_id);
		$this->standard_id = $assemblyGroup->standard_id;
		
		return parent::beforeValidate();
	}

}