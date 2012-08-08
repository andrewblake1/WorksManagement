<?php

/**
 * This is the model class for table "project_to_generic_project_type".
 *
 * The followings are the available columns in table 'project_to_generic_project_type':
 * @property string $id
 * @property integer $generic_project_type_id
 * @property string $project_id
 * @property string $generic_id
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property Generic $generic
 * @property Staff $staff
 * @property GenericProjectType $genericProjectType
 * @property Project $project
 */
class ProjectToGenericProjectType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchGenericProjectType;
	public $searchProject;
	public $searchGeneric;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectToGenericProjectType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project_to_generic_project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('generic_project_type_id, project_id, generic_id, staff_id', 'required'),
			array('generic_project_type_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('project_id, generic_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, searchGenericProjectType, searchProject, searchGeneric, searchStaff', 'safe', 'on'=>'search'),
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
			'generic' => array(self::BELONGS_TO, 'Generic', 'generic_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'genericProjectType' => array(self::BELONGS_TO, 'GenericProjectType', 'generic_project_type_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project To Generic Project Type',
			'generic_project_type_id' => 'Generic Project Type (Client/Project type/Generic type)',
			'searchGenericProjectType' => 'Generic Project Type (Client/Project type/Generic type)',
			'project_id' => 'Project',
			'searchProject' => 'Project',
			'generic_id' => 'Generic',
			'searchGeneric' => 'Generic',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$this->compositeCriteria($criteria, array(
			'genericProjectType.client.name',
			'genericProjectType.projectType.description',
			'genericProjectType.genericType.description',
			), $this->searchGenericProjectType);
		$criteria->compare('project.description',$this->searchProject,true);
		$criteria->compare('generic.id',$this->searchGeneric,true);
		
		$criteria->with = array('genericProjectType.genericType','project','generic');

		$criteria->select=array(
			'id',
			"CONCAT_WS('$delimiter',
				genericProjectType.client.name,
				genericProjectType.projectType.description,
				genericProjectType.genericType.description,
				) AS searchGenericProjectType",
			'project.description AS searchProject',
			'generic.id AS searchGeneric',
		);

		return $criteria;
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchGenericProjectType', 'searchProject', 'searchGeneric');
	}
}