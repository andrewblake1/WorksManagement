<?php

/**
 * This is the model class for table "project_type".
 *
 * The followings are the available columns in table 'project_type':
 * @property integer $id
 * @property string $description
 * @property integer $client_id
 * @property string $template_project_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property GenericProjectType[] $genericProjectTypes
 * @property Project[] $projects
 * @property Staff $staff
 * @property Project $templateProject
 * @property Client $client
 */
class ProjectType extends ActiveRecord
{
	/**
	 * @var string search variables - foreign key lookups sometimes composite.
	 * these values are entered by user in admin view to search
	 */
	public $searchClient;
	public $searchTemplateProject;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectType the static model class
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
		return 'project_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, client_id, staff_id', 'required'),
			array('client_id, deleted, staff_id', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>64),
			array('template_project_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, client_id, deleted, searchTemplateProject, searchClient, searchStaff, template_project_id', 'safe', 'on'=>'search'),
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
			'genericProjectTypes' => array(self::HAS_MANY, 'GenericProjectType', 'project_type_id'),
			'projects' => array(self::HAS_MANY, 'Project', 'project_type_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
			'templateProject' => array(self::BELONGS_TO, 'Project', 'template_project_id'),
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'id' => 'Project Type',
			'client_id' => 'Client',
			'searchClient' => 'Client',
			'template_project_id' => 'Template Project',
			'searchTemplateProject' => 'Template Project',
		));
	}

	/**
	 * @return CDbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('template_project_id',$this->template_project_id,true);
		$criteria->compare('templateProject.description',$this->searchTemplateProject,true);
		$criteria->compare('client.name',$this->searchClient,true);
		
		$criteria->with = array('client', 'templateProject');

		$criteria->select=array(
			'id',
			'description',
			'client.name AS searchClient',
			'template_project_id',
		);

		return $criteria;
	}

	/**
	 * @return array the list of columns to be concatenated for use in drop down lists
	 */
	public static function getDisplayAttr()
	{
		return array(
			'client'=>'name',
			'description',
		);
	}

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{
		return array('searchClient', 'searchTemplateProject');
	}

}
