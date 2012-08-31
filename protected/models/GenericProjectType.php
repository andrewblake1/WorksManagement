<?php

/**
 * This is the model class for table "generic_project_type".
 *
 * The followings are the available columns in table 'generic_project_type':
 * @property integer $id
 * @property integer $project_type_id
 * @property integer $genericprojectcategory_id
 * @property integer $generic_type_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericType $genericType
 * @property Genericprojectcategory $genericprojectcategory
 * @property Staff $staff
 * @property ProjectType $projectType
 * @property ProjectToGenericProjectType[] $projectToGenericProjectTypes
 */
class GenericProjectType extends ActiveRecord
{
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Custom type';

	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchProjectType;
	public $searchGenericprojectcategory;
	public $searchGenericType;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'generic_project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_type_id, generic_type_id, staff_id', 'required'),
			array('project_type_id, genericprojectcategory_id, generic_type_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_type_id, searchProjectType, searchGenericprojectcategory, searchGenericType, searchStaff', 'safe', 'on'=>'search'),
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
			'genericType' => array(self::BELONGS_TO, 'GenericType', 'generic_type_id'),
			'genericprojectcategory' => array(self::BELONGS_TO, 'Genericprojectcategory', 'genericprojectcategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'projectType' => array(self::BELONGS_TO, 'ProjectType', 'project_type_id'),
			'projectToGenericProjectTypes' => array(self::HAS_MANY, 'ProjectToGenericProjectType', 'generic_project_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Generic project type',
			'project_type_id' => 'Client/Project type',
			'searchProjectType' => 'Client/Project type',
			'genericprojectcategory_id' => 'Project category',
			'searchGenericprojectcategory' => 'Project category',
			'generic_type_id' => 'Custom type',
			'searchGenericType' => 'Custom type',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		// select
		$delimiter = Yii::app()->params['delimiter']['display'];
		$criteria->select=array(
			't.genericprojectcategory_id',
			't.generic_type_id',
			'projectType.description AS searchProjectType',
			'genericprojectcategory.name AS searchGenericprojectcategory',
			'genericType.description AS searchGenericType',
		);

		// where
		$criteria->compare('projectType.description',$this->searchProjectType,true);
		$criteria->compare('genericprojectcategory.name',$this->searchGenericprojectcategory,true);
		$criteria->compare('genericType.description',$this->searchGenericType,true);
		$criteria->compare('t.project_type_id',$this->project_type_id);
		
		//join 
		$criteria->with = array(
			'projectType',
			'genericprojectcategory',
			'genericType',
			);

		return $criteria;
	}

	public function getAdminColumns()
	{
		$columns[] = static::linkColumn('searchGenericprojectcategory', 'Genericprojectcategory', 'genericprojectcategory_id');
		$columns[] = static::linkColumn('searchGenericType', 'GenericType', 'generic_type_id');
		
		return $columns;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'genericType->description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchGenericprojectcategory', 'searchGenericType');
	}

}

?>