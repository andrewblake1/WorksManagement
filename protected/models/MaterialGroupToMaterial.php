<?php

/**
 * This is the model class for table "tbl_material_group_to_material".
 *
 * The followings are the available columns in table 'tbl_material_group_to_material':
 * @property integer $id
 * @property integer $material_group_id
 * @property integer $material_id
 * @property integer $standard_id
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property Material $material
 * @property MaterialGroup $standard
 * @property MaterialGroup $materialGroup
 * @property User $updatedBy
 * @property TaskToMaterialToAssemblyToMaterialGroup[] $taskToMaterialToAssemblyToMaterialGroups
 * @property TaskToMaterialToAssemblyToMaterialGroup[] $taskToMaterialToAssemblyToMaterialGroups1
 * @property TaskToMaterialToAssemblyToMaterialGroup[] $taskToMaterialToAssemblyToMaterialGroups2
 * @property TaskToMaterialToTaskTemplateToMaterialGroup[] $taskToMaterialToTaskTemplateToMaterialGroups
 * @property TaskToMaterialToTaskTemplateToMaterialGroup[] $taskToMaterialToTaskTemplateToMaterialGroups1
 */
class MaterialGroupToMaterial extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchMaterialDescription;
	public $searchMaterialUnit;
	public $searchMaterialAlias;

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
		return array_merge(parent::rules(), array(
			array('material_id, material_group_id, standard_id,', 'required'),
			array('material_id, material_group_id, standard_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
//			array('id, searchMaterialDescription, searchMaterialUnit, searchMaterialAlias, material_id, material_group_id', 'safe', 'on'=>'search'),
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
            'material' => array(self::BELONGS_TO, 'Material', 'material_id'),
            'standard' => array(self::BELONGS_TO, 'MaterialGroup', 'standard_id'),
            'materialGroup' => array(self::BELONGS_TO, 'MaterialGroup', 'material_group_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'taskToMaterialToAssemblyToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'material_id'),
            'taskToMaterialToAssemblyToMaterialGroups1' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'material_group_id'),
            'taskToMaterialToAssemblyToMaterialGroups2' => array(self::HAS_MANY, 'TaskToMaterialToAssemblyToMaterialGroup', 'material_group_to_material_id'),
            'taskToMaterialToTaskTemplateToMaterialGroups' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'material_id'),
            'taskToMaterialToTaskTemplateToMaterialGroups1' => array(self::HAS_MANY, 'TaskToMaterialToTaskTemplateToMaterialGroup', 'material_group_to_material_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'material_group_id' => 'Material Group',
//			'material_id' => 'Material group/Material/Unit/Alias',
			'searchMaterialDescription' => 'Material',
			'searchMaterialUnit' => 'Unit',
			'searchMaterialAlias' => 'Alias',
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
			't.material_group_id',
			'material.description AS searchMaterialDescription',
			'material.unit AS searchMaterialUnit',
			'material.alias AS searchMaterialAlias',
		);

		// where
		$criteria->compare('material_group_id',$this->material_group_id);
		$criteria->compare('material.description',$this->searchMaterialDescription,true);
		$criteria->compare('material.unit',$this->searchMaterialUnit,true);
		$criteria->compare('material.alias',$this->searchMaterialAlias,true);
 
		// with
		$criteria->with = array(
			'material',
		);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = 'searchMaterialDescription';
 		$columns[] = 'searchMaterialUnit';
 		$columns[] = 'searchMaterialAlias';
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
//			'materialGroup->description',
			'searchMaterialDescription',
			'searchMaterialUnit',
			'searchMaterialAlias',
		);
	}

	public function beforeValidate()
	{
		$materialGroup = MaterialGroup::model()->findByPk($this->material_group_id);
		$this->standard_id = $materialGroup->standard_id;
		
		return parent::beforeValidate();
	}

}