<?php

/**
 * This is the model class for table "assembly_to_assembly".
 *
 * The followings are the available columns in table 'assembly_to_assembly':
 * @property integer $id
 * @property integer $parent_assembly_id
 * @property integer $child_assembly_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Assembly $parentAssembly
 * @property Assembly $childAssembly
 * @property Staff $staff
 */
class AssemblyToAssembly extends ActiveRecord
{

	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Sub assembly';

	public $searchChildAssembly;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assembly_to_assembly';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_assembly_id, child_assembly_id, staff_id', 'required'),
			array('parent_assembly_id, child_assembly_id, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('searchChildAssembly, id, parent_assembly_id, child_assembly_id, staff_id', 'safe', 'on'=>'search'),
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
			'parentAssembly' => array(self::BELONGS_TO, 'Assembly', 'parent_assembly_id'),
			'childAssembly' => array(self::BELONGS_TO, 'Assembly', 'child_assembly_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Assembly',
			'parent_assembly_id' => 'Parent Assembly',
			'child_assembly_id' => 'Child assembly',
			'searchChildAssembly' => 'Child assembly',
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
			't.id',
			't.parent_assembly_id',
			't.child_assembly_id',
			"CONCAT_WS('$delimiter',
				childAssembly.description,
				childAssembly.alias
				) AS searchChildAssembly",
		);

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.parent_assembly_id',$this->parent_assembly_id);
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
		
		return $columns;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchChildAssembly');
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
	
}